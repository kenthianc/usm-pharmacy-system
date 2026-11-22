<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;

it('renders the welcome landing page with AI health assistant and guest counter without embedded store catalog', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('USM Hospital');
    $response->assertSee('Your health,');
    $response->assertSee('50/50 messages left');
    $response->assertSee('Pharmacy Store');
    $response->assertDontSee('Showing 34 products');
});

it('renders the pharmacy storefront on its own dedicated page with public access and detail modals', function () {
    $response = $this->get(route('medicines'));

    $response->assertOk();
    $response->assertSee('USM Hospital Pharmacy');
    $response->assertSee('Product Catalog');
    $response->assertDontSee('You are browsing as a guest');
    $response->assertDontSee('Sign in to Inquire');
    $response->assertSee('View Details');
    $response->assertSee('Showing');
    $response->assertSee('products');
});

it('renders the pharmacy storefront with full access for authenticated users as well', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('medicines'));

    $response->assertOk();
    $response->assertDontSee('You are browsing as a guest');
    $response->assertDontSee('Sign in to Inquire');
    $response->assertSee('View Details');
});

it('configures the AI assistant with OPD referral and clinic hours instead of clinical diagnosis', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Infirmary &amp; OPD Hours', false);
    $response->assertSee('Discounts &amp; Prescriptions', false);
    $response->assertSee('USM Hospital &amp; Infirmary Consultation Hours', false);
    $response->assertSee('8:00 AM &ndash; 5:00 PM', false);
    $response->assertDontSee('First aid help');
});

it('correctly answers specific medicine questions and refers symptoms via chatbot api endpoint', function () {
    $this->seed(DatabaseSeeder::class);

    // 1. Availability / Stock inquiry
    $resStock = $this->postJson(route('chatbot.message'), ['message' => 'may amoxyclav ba?']);
    $resStock->assertOk();
    $resStock->assertJsonFragment(['source' => 'local']);
    expect($resStock->json('reply'))
        ->toContain('Co-Amoxiclav 625mg')
        ->toContain('Pharmacy Store')
        ->toContain('Prescription-Only');

    // 2. "Gamot diba" classification inquiry
    $resClass = $this->postJson(route('chatbot.message'), ['message' => 'gamot diba ang amoxyclav?']);
    $resClass->assertOk();
    expect($resClass->json('reply'))
        ->toContain('Oo, ang Co-Amoxiclav')
        ->toContain('gamot (Antibiotic)')
        ->toContain('Pharmacy Store');

    // 3. Price inquiry
    $resPrice = $this->postJson(route('chatbot.message'), ['message' => 'magkano ang amoxyclav?']);
    $resPrice->assertOk();
    expect($resPrice->json('reply'))
        ->toContain('₱28.00')
        ->toContain('USM Students')
        ->toContain('Senior Citizens & PWD');

    // 4. Clinical symptom inquiry refused with OPD referral
    $resSymptom = $this->postJson(route('chatbot.message'), ['message' => 'masakit ang ulo ko anong gamot?']);
    $resSymptom->assertOk();
    expect($resSymptom->json('reply'))
        ->toContain('Hindi ako maaaring magbigay ng medical advice')
        ->toContain('USM Hospital & University Infirmary / Outpatient Department (OPD)')
        ->toContain('8:00 AM – 5:00 PM');
});
