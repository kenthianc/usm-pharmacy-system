<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class GoogleAuthController extends Controller
{
    /**
     * Approved institutional domains for student patient registration.
     */
    public const APPROVED_DOMAINS = [
        'usm.edu.ph',
        'student.usm.edu.ph',
    ];

    /**
     * Redirect the user to the Google OAuth authorization page.
     */
    public function redirectToGoogle(Request $request): RedirectResponse|View
    {
        $studentId = $request->query('student_id') ?? $request->input('student_id');
        if ($studentId) {
            session(['google_oauth_student_id' => trim((string) $studentId)]);
        }

        $clientId = config('services.google.client_id', env('GOOGLE_CLIENT_ID'));
        $redirectUri = config('services.google.redirect', env('GOOGLE_REDIRECT_URI', route('auth.google.callback')));

        // If credentials are not configured in local/dev environment, offer the institutional test intake
        if (empty($clientId) || $clientId === 'dummy_client_id') {
            return $this->handleDevSimulationRedirect($request);
        }

        $state = Str::random(40);
        session(['oauth_state' => $state]);

        $query = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid profile email',
            'hd' => 'usm.edu.ph',
            'state' => $state,
            'prompt' => 'select_account',
        ]);

        return redirect('https://accounts.google.com/o/oauth2/v2/auth?'.$query);
    }

    /**
     * Handle the callback from Google OAuth.
     */
    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        // 1. Check for error response from Google
        if ($request->has('error')) {
            return redirect()->route('register')->withErrors([
                'email' => 'Google authorization was cancelled: '.e($request->query('error_description', $request->query('error'))),
            ]);
        }

        $code = $request->query('code');
        $simulatedEmail = $request->input('email');

        // Allow dev simulation if code is empty and simulated email is provided
        if (empty($code) && ! empty($simulatedEmail) && app()->environment(['local', 'testing'])) {
            return $this->processGoogleUserData([
                'email' => $simulatedEmail,
                'name' => $request->input('name', 'USM Student'),
                'sub' => 'google_sim_'.Str::random(12),
            ]);
        }

        if (empty($code)) {
            return redirect()->route('register')->withErrors([
                'email' => 'No authorization code was returned from Google.',
            ]);
        }

        $clientId = config('services.google.client_id', env('GOOGLE_CLIENT_ID'));
        $clientSecret = config('services.google.client_secret', env('GOOGLE_CLIENT_SECRET'));
        $redirectUri = config('services.google.redirect', env('GOOGLE_REDIRECT_URI', route('auth.google.callback')));

        // Exchange code for access token
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'code' => $code,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ]);

        if (! $response->successful()) {
            return redirect()->route('register')->withErrors([
                'email' => 'Unable to authenticate with Google. Please try again or contact IT support.',
            ]);
        }

        $tokenData = $response->json();
        $accessToken = $tokenData['access_token'] ?? null;

        if (! $accessToken) {
            return redirect()->route('register')->withErrors([
                'email' => 'Invalid authentication token received from Google.',
            ]);
        }

        // Fetch user profile from Google UserInfo endpoint
        $userProfileResponse = Http::withToken($accessToken)->get('https://www.googleapis.com/oauth2/v3/userinfo');

        if (! $userProfileResponse->successful()) {
            return redirect()->route('register')->withErrors([
                'email' => 'Failed to retrieve Google profile data.',
            ]);
        }

        $googleUser = $userProfileResponse->json();

        return $this->processGoogleUserData($googleUser);
    }

    /**
     * Process Google User data with strict institutional domain validation.
     *
     * @param  array<string, mixed>  $googleUser
     */
    public function processGoogleUserData(array $googleUser): RedirectResponse
    {
        $email = strtolower(trim((string) ($googleUser['email'] ?? '')));
        $name = trim((string) ($googleUser['name'] ?? 'USM Student'));

        if (empty($email)) {
            return redirect()->route('register')->withErrors([
                'email' => 'Google account did not return a valid email address.',
            ]);
        }

        // 2. Validate institutional domain
        if (! $this->isApprovedInstitutionalDomain($email)) {
            return redirect()->route('register')->withErrors([
                'email' => "Access denied. Only verified USM institutional Google accounts (@usm.edu.ph or @student.usm.edu.ph) are permitted for Student registration. (Account '{$email}' is unauthorized).",
            ]);
        }

        // 3. Find or create the User
        $patientRole = Role::firstOrCreate([
            'name' => 'patient',
            'guard_name' => 'web',
        ]);

        $user = User::where('email', $email)->first();

        if (! $user) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(Str::random(32)),
                'role_id' => $patientRole->id,
            ]);

            $user->assignRole($patientRole);
            event(new Registered($user));
        }

        // 4. Find or create the Patient record
        $studentId = session()->pull('google_oauth_student_id') ?? 'STU-'.str_pad((string) $user->id, 5, '0', STR_PAD_LEFT);

        $patient = Patient::where('user_id', $user->id)->first();
        if (! $patient) {
            Patient::create([
                'user_id' => $user->id,
                'patient_type' => 'student',
                'id_number' => $studentId,
                'contact_number' => null,
            ]);
        } else {
            // Ensure patient classification reflects student
            $patient->update([
                'patient_type' => 'student',
                'id_number' => $patient->id_number ?: $studentId,
            ]);
        }

        Auth::login($user);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Check if the given email belongs to an approved institutional domain.
     */
    public function isApprovedInstitutionalDomain(string $email): bool
    {
        $domain = substr(strrchr($email, '@'), 1);

        return in_array(strtolower((string) $domain), self::APPROVED_DOMAINS, true);
    }

    /**
     * Fallback dev simulation when Google credentials are not yet configured.
     */
    protected function handleDevSimulationRedirect(Request $request): RedirectResponse
    {
        $studentId = $request->query('student_id') ?? $request->input('student_id');

        // Redirect back to registration with a test simulation session prompt
        return redirect()->route('register', [
            'track' => 'student',
            'student_id' => $studentId,
            'demo_notice' => 'google_unconfigured',
        ]);
    }
}
