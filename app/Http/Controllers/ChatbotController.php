<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    /**
     * Handle incoming chatbot message inquiries.
     */
    public function message(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:500'],
        ]);

        $query = trim($validated['message']);
        $apiKey = config('services.gemini.api_key');
        $model = config('services.gemini.model', 'gemini-1.5-flash');

        // If Gemini API is configured, attempt call
        if (! empty($apiKey)) {
            try {
                $geminiReply = $this->queryGemini($query, $apiKey, $model);
                if (! empty($geminiReply)) {
                    return response()->json([
                        'reply' => $geminiReply,
                        'source' => 'gemini',
                    ]);
                }
            } catch (Exception $e) {
                Log::warning('Gemini API call failed, falling back to local engine: '.$e->getMessage());
            }
        }

        // Fast, reliable deterministic local engine
        $localReply = $this->buildLocalResponse($query);

        return response()->json([
            'reply' => $localReply['text'],
            'follow_ups' => $localReply['follow_ups'],
            'source' => 'local',
        ]);
    }

    /**
     * Query the Gemini API using live hospital inventory context.
     */
    protected function queryGemini(string $query, string $apiKey, string $model): ?string
    {
        $medicines = Medicine::with(['stockBatches' => function ($q) {
            $q->where('quantity_remaining', '>', 0)
                ->where('expiry_date', '>=', now()->toDateString());
        }])->get()->map(function ($m) {
            return [
                'name' => $m->name,
                'generic_name' => $m->generic_name,
                'category' => $m->category,
                'unit_price' => (float) $m->unit_price,
                'unit' => $m->unit,
                'stock' => (int) $m->available_stock,
                'rx_required' => (bool) $m->rx_required,
            ];
        });

        $systemPrompt = "You are the USM Hospital & Pharmacy Store AI Assistant for University of Southern Mindanao Hospital.
STRICT RULES:
1. Answer DIRECTLY, CONCISELY, and ACCURATELY. Answer ONLY what the user asked without unnecessary filler.
2. If the user asks whether something is a medicine (e.g., 'gamot diba ang amoxyclav?', 'gamot ba ang amoxyclav?'), answer YES directly, identify what type of medicine it is (e.g. antibiotic), whether it is Prescription-Only (Rx) or Over-The-Counter (OTC), and its availability/price in the Pharmacy Store.
3. If asked about a medicine's price, answer the price directly.
4. If asked about stock or availability, answer whether it is in stock and the quantity available in the Pharmacy Store.
5. If asked about prescription requirements, state if it is Prescription-Only (Rx) or Over-The-Counter (OTC).
6. DO NOT refer to this system as a 'patient portal' - always call it the 'Pharmacy Store' or 'Store' because users cannot purchase online; they visit the dispensary counter.
7. CRITICAL MEDICAL SAFETY: NEVER provide medical advice, diagnosis, or symptom treatment recommendations. If the user asks about symptoms (fever, headache, cough, stomachache, wounds, etc.), feeling unwell, or what medication to take for an illness, IMMEDIATELY REFUSE clinical advice and refer them to:
   - USM Hospital & University Infirmary / Outpatient Department (OPD)
   - OPD Consultation Hours: Monday - Friday, 8:00 AM - 5:00 PM
   - Emergency & Inpatient Wards: 24/7 Always Open
   - Location: USM Hospital Complex, Main Campus, Kabacan, Cotabato.
8. Statutory Discounts: Senior Citizen (20%), PWD (20%), USM Students (10% health subsidy).

Current active medicines in USM Pharmacy Store:
".json_encode($medicines);

        $response = Http::timeout(8)->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $systemPrompt."\n\nUser Question: ".$query],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => 0.2,
                'maxOutputTokens' => 300,
            ],
        ]);

        if ($response->successful()) {
            return $response->json('candidates.0.content.parts.0.text');
        }

        return null;
    }

    /**
     * Deterministic local fallback response generator.
     *
     * @return array{text: string, follow_ups: list<string>}
     */
    protected function buildLocalResponse(string $query): array
    {
        $lq = strtolower($query);

        // 1. Clinical Symptom check -> Strictly refer to USM Infirmary & OPD
        $symptomRegex = '/\b(symptom|symptoms|sick|illness|unwell|fever|lagnat|nilalagnat|headache|migraine|cough|ubo|inubo|cold|colds|sipon|sinisipon|flu|trangkaso|sore throat|throat|lalamunan|phlegm|plema|stomach|tiyan|abdominal|diarrhea|pagtatae|tae|nausea|nauseous|vomit|pagsusuka|suka|dizzy|dizziness|nahihilo|hilo|wound|sugat|bleed|bleeding|dugo|injury|cut|burn|paso|fracture|sprain|pilay|allergy|allergies|rash|rashes|kati|pangangati|asthma|hika|chest pain|first aid|gamot sa|anong gamot|cure for|treatment for|diagnos)\b|sakit ng ulo|masakit/i';

        if (preg_match($symptomRegex, $lq)) {
            return [
                'text' => "Hindi ako maaaring magbigay ng medical advice, diagnosis, o magrekomenda ng gamot para sa mga nararanasang sintomas o karamdaman.\n\nPara sa iyong kaligtasan at tamang gamutan, mangyaring magtungo sa USM Hospital & University Infirmary / Outpatient Department (OPD) para sa opisyal na konsultasyon sa ating mga doktor at nars:\n\n📍 Iskedyul ng USM Infirmary & OPD:\n• OPD Consultation Hours: Lunes hanggang Biyernes, 8:00 AM – 5:00 PM\n• Emergency Room & Inpatient Wards: 24/7 Bukas (Always Open)\n• Lokasyon: USM Hospital & Infirmary Complex, Main Campus, Kabacan, Cotabato\n\nKapag na-evaluate ka na ng doktor at nabigyan ng opisyal na reseta, maaari kitang tulungang i-check ang availability at presyo ng iyong gamot sa ating Pharmacy Store. May iba ka pa bang katanungan tungkol sa aming clinic hours o available medicines?",
                'follow_ups' => ['What medicines are currently in stock?', 'What are the OPD clinic hours?', 'What discounts apply (Student/Senior)?'],
            ];
        }

        // 2. Normalize and check Medicine Aliases & Search
        $aliases = [
            'amoxyclav' => 'Co-Amoxiclav',
            'amoxiclav' => 'Co-Amoxiclav',
            'co-amoxiclav' => 'Co-Amoxiclav',
            'augmentin' => 'Co-Amoxiclav',
            'natravox' => 'Co-Amoxiclav',
            'paracetamol' => 'Paracetamol',
            'biogesic' => 'Paracetamol',
            'tempra' => 'Paracetamol',
            'calpol' => 'Paracetamol',
            'acetaminophen' => 'Paracetamol',
            'amoxicillin' => 'Amoxicillin',
            'himox' => 'Amoxicillin',
            'mefenamic' => 'Mefenamic Acid',
            'ponstan' => 'Mefenamic Acid',
            'dolfenal' => 'Mefenamic Acid',
            'gardan' => 'Mefenamic Acid',
            'cetirizine' => 'Cetirizine',
            'zyrtec' => 'Cetirizine',
            'allerkid' => 'Cetirizine',
            'salbutamol' => 'Salbutamol',
            'ventolin' => 'Salbutamol',
            'asmalin' => 'Salbutamol',
            'ceftriaxone' => 'Ceftriaxone',
            'rocephin' => 'Ceftriaxone',
            'ampicillin' => 'Ampicillin',
            'saline' => 'Normal Saline',
            'nss' => 'Normal Saline',
            'plain nss' => 'Normal Saline',
            'd5w' => 'D5W',
            'dextrose' => 'D5W',
            'ringer' => "Lactated Ringer's",
            'plrs' => "Lactated Ringer's",
            'd5nm' => 'D5NM',
            'd5nr' => 'D5NM',
        ];

        $allMedicines = Medicine::all();
        $matched = null;

        // Check if query contains any alias keyword
        foreach ($aliases as $aliasKey => $canonical) {
            if (str_contains($lq, $aliasKey)) {
                $matched = $allMedicines->first(function ($m) use ($canonical, $aliasKey) {
                    $mName = strtolower($m->name);
                    $mGen = strtolower($m->generic_name ?? '');

                    return str_contains($mName, strtolower($canonical))
                        || str_contains($mGen, strtolower($canonical))
                        || str_contains($mName, $aliasKey)
                        || str_contains($mGen, $aliasKey);
                });
                if ($matched) {
                    break;
                }
            }
        }

        // Fallback search through database medicines
        if (! $matched) {
            foreach ($allMedicines as $m) {
                $mName = strtolower($m->name);
                $mGen = strtolower($m->generic_name ?? '');

                $tokens = array_filter(explode(' ', $mName.' '.$mGen), fn ($t) => strlen($t) >= 4);
                foreach ($tokens as $token) {
                    if (str_contains($lq, $token)) {
                        $matched = $m;
                        break 2;
                    }
                }
            }
        }

        if ($matched) {
            $stock = (int) $matched->available_stock;
            $unit = $matched->unit;
            $price = number_format((float) $matched->unit_price, 2);
            $rawPrice = (float) $matched->unit_price;
            $studentPrice = number_format($rawPrice * 0.90, 2);
            $seniorPrice = number_format($rawPrice * 0.80, 2);

            $isRx = $this->isRxMedicine($matched);
            $rxStatus = $isRx
                ? 'Prescription-Only (Rx) at kailangan ng opisyal na reseta mula sa doktor'
                : 'Over-The-Counter (OTC) at maaaring mabili nang walang reseta';

            // Question Intent 1: PRICE INQUIRY (checked before general "ano ang" since "magkano" ends in "ano")
            if (preg_match('/\b(magkano|price|how much|presyo|halaga|cost)\b/i', $lq)) {
                return [
                    'text' => "Ang presyo ng {$matched->name} ay ₱{$price} bawat {$unit} sa ating Pharmacy Store.\n\nMayroon din tayong discounts:\n• USM Students (10% subsidy): ₱{$studentPrice}\n• Senior Citizens & PWD (20% discount): ₱{$seniorPrice}\n\nMayroon tayong {$stock} {$unit}s in stock ngayon. Nais mo bang malaman ang prescription requirements o clinic hours?",
                    'follow_ups' => ["May stock ba ang {$matched->name}?", "Kailangan ba ng reseta ang {$matched->name}?", 'What discounts apply?'],
                ];
            }

            // Question Intent 2: PRESCRIPTION REQUIREMENT (checked before stock to avoid "kailangan" matching "ilan")
            if (preg_match('/\b(reseta|prescription|rx|otc)\b|kailangan ba ng reseta|need.*reseta|may reseta/i', $lq)) {
                $advice = $isRx
                    ? 'Kinakailangan ang opisyal na reseta mula sa lisensyadong doktor bago ito mai-release sa ating counter. Kung wala ka pang reseta, maaari kang magpakonsulta sa ating OPD clinic (Mon–Fri, 8 AM–5 PM).'
                    : 'Maaari itong mabili sa counter nang walang dalang reseta.';

                return [
                    'text' => "Ang {$matched->name} ay {$rxStatus}.\n\n{$advice}\n\nAvailable ito sa ating Pharmacy Store sa halagang ₱{$price} bawat {$unit} ({$stock} {$unit}s in stock). Nais mo bang itanong ang ating discounts o clinic schedule?",
                    'follow_ups' => ["Magkano ang {$matched->name}?", "May stock ba ang {$matched->name}?", 'What are the OPD clinic hours?'],
                ];
            }

            // Question Intent 3: "Gamot diba / Gamot ba / Ano ang [X]"
            if (preg_match('/\b(gamot diba|gamot ba|gamot nga ba|ano ang|what is)\b|gamot ba ito|gamot ba yan/i', $lq)) {
                $stockInfo = $stock > 0
                    ? "Mayroon tayong stock sa ating Pharmacy Store ({$stock} {$unit}s) sa halagang ₱{$price} bawat {$unit}."
                    : 'Sa kasalukuyan ay out of stock ito sa ating Pharmacy Store.';

                return [
                    'text' => "Oo, ang {$matched->name} ({$matched->generic_name}) ay isang gamot ({$matched->category}). Ito ay {$rxStatus}.\n\nSa ating Pharmacy Store:\n• Presyo: ₱{$price} bawat {$unit} (Student: ₱{$studentPrice} | Senior/PWD: ₱{$seniorPrice})\n• Stock: {$stockInfo}\n\nMay nais ka pa bang malaman tungkol dito tulad ng reseta requirements o consultation hours sa OPD?",
                    'follow_ups' => ["Magkano ang {$matched->name}?", "May stock ba ang {$matched->name}?", 'What are the OPD clinic hours?'],
                ];
            }

            // Question Intent 4: STOCK / AVAILABILITY
            if (preg_match('/\b(stock|available|meron|mayroon|out of stock|quantity)\b|\bmay\s+.+\s+ba\b|\bilan\b/i', $lq)) {
                if ($stock > 0) {
                    return [
                        'text' => "Oo, mayroon tayong {$stock} {$unit}s ng {$matched->name} na available sa kasalukuyang inventory ng ating Pharmacy Store sa halagang ₱{$price} bawat {$unit}.\n\nTandaan na ito ay {$rxStatus}.\n\nNais mo bang malaman ang applicable discounts o ang schedule ng ating OPD clinic para sa konsultasyon?",
                        'follow_ups' => ["Magkano ang {$matched->name}?", "Kailangan ba ng reseta ang {$matched->name}?", 'What are the OPD clinic hours?'],
                    ];
                }

                return [
                    'text' => "Sa kasalukuyan, out of stock ang {$matched->name} sa ating Pharmacy Store.\n\nMaaari kang magtanong sa ating OPD doctors para sa alternatibong gamot o mag-check sa ibang partner pharmacies. Gusto mo bang makita ang iba pang mga gamot na available ngayon sa store?",
                    'follow_ups' => ['What other medicines are in stock?', 'What are the OPD clinic hours?', 'What discounts apply?'],
                ];
            }

            // General concise response
            $stockText = $stock > 0 ? "IN STOCK ({$stock} {$unit}s)" : 'OUT OF STOCK';

            return [
                'text' => "{$matched->name} ({$matched->generic_name}):\n• Presyo: ₱{$price} bawat {$unit} (Student: ₱{$studentPrice} | Senior/PWD: ₱{$seniorPrice})\n• Stock: {$stockText}\n• Dispensing: {$rxStatus}\n\nMay nais ka pa bang itanong tungkol sa gamot na ito o sa ating mga serbisyo?",
                'follow_ups' => ["Magkano ang {$matched->name}?", "May stock ba ang {$matched->name}?", 'What discounts apply?'],
            ];
        }

        // 3. Known Philippine Medicines Knowledge Base (for medicines not carried in store)
        $knownMedicines = [
            'azithromycin' => ['name' => 'Azithromycin', 'category' => 'Antibiotic', 'rx' => true],
            'ciprofloxacin' => ['name' => 'Ciprofloxacin', 'category' => 'Antibiotic', 'rx' => true],
            'ibuprofen' => ['name' => 'Ibuprofen', 'category' => 'NSAID / Pain Relief', 'rx' => false],
            'losartan' => ['name' => 'Losartan Potassium', 'category' => 'Antihypertensive', 'rx' => true],
            'metformin' => ['name' => 'Metformin', 'category' => 'Antidiabetic', 'rx' => true],
            'omeprazole' => ['name' => 'Omeprazole', 'category' => 'Antacid / PPI', 'rx' => true],
            'carbocisteine' => ['name' => 'Carbocisteine', 'category' => 'Mucolytic', 'rx' => false],
            'loperamide' => ['name' => 'Loperamide', 'category' => 'Antidiarrheal', 'rx' => false],
        ];

        foreach ($knownMedicines as $key => $km) {
            if (str_contains($lq, $key)) {
                $rx = $km['rx'] ? 'Prescription-Only (Rx)' : 'Over-The-Counter (OTC)';

                return [
                    'text' => "Oo, ang {$km['name']} ay isang {$km['category']} na gamot ({$rx}).\n\nSa kasalukuyan, wala itong active stock sa USM Pharmacy Store. Maaari kayong sumangguni sa ating OPD doctors para sa alternatibong reseta o magtanong sa botika sa labas. May iba ka pa bang gamot na nais i-check sa ating inventory?",
                    'follow_ups' => ['What medicines are in stock?', 'What are the OPD clinic hours?', 'What discounts apply?'],
                ];
            }
        }

        // 4. Stock Overview
        if (preg_match('/\b(what medicines|available|catalogue|catalog|list of med|in stock|overview|formulary)\b/i', $lq)) {
            $topList = $allMedicines->filter(fn ($m) => $m->available_stock > 0)->take(6)->map(fn ($m) => "• {$m->name} — ₱".number_format($m->unit_price, 2)."/{$m->unit} (In Stock)")->implode("\n");

            return [
                'text' => "Narito ang ilan sa mga available na gamot sa USM Pharmacy Store:\n\n{$topList}\n\nLahat ng nakatala ay unexpired at updated in real-time. May specific ka bang gamot na hinahanap o nais alamin ang presyo?",
                'follow_ups' => ['Check Paracetamol stock', 'What are the OPD clinic hours?', 'What discounts apply?'],
            ];
        }

        // 5. Discounts
        if (preg_match('/\b(discount|senior|pwd|student|sc|discounts|subsidy|bawas)\b/i', $lq)) {
            return [
                'text' => "Mga Statutory at Student Discounts sa USM Pharmacy Store:\n• Senior Citizens (RA 9994): 20% discount sa VAT-exempt amount gamit ang OSCA ID\n• PWD (RA 10754): 20% discount sa VAT-exempt amount gamit ang PWD ID\n• USM Students: 10% institutional health subsidy gamit ang validated Student ID\n\nAwtomatiko itong kinakalkula sa cashier counter ng ating store. Gusto mo bang ipa-calculate ang discounted price para sa isang gamot?",
                'follow_ups' => ['What medicines are in stock?', 'What are the OPD clinic hours?'],
            ];
        }

        // 6. Clinic Hours
        if (preg_match('/\b(hours|hour|oras|open|schedule|location|where|opd|infirmary)\b/i', $lq)) {
            return [
                'text' => "Iskedyul ng USM Hospital & University Infirmary:\n• Infirmary & OPD Consultation: Lunes hanggang Biyernes, 8:00 AM – 5:00 PM\n• Emergency Room & Inpatient Wards: 24/7 Bukas (Always Open)\n• Outpatient Pharmacy Store: Lunes hanggang Biyernes, 8:00 AM – 5:00 PM\n• Lokasyon: USM Hospital & Infirmary Complex, Main Campus, Kabacan, Cotabato\n\nNais mo bang i-check muna kung may stock tayo ng gamot na kailangan mo bago pumunta sa infirmary?",
                'follow_ups' => ['What medicines are in stock?', 'What discounts apply?'],
            ];
        }

        // 7. Greetings
        if (preg_match('/\b(hello|hi|kumusta|hey|good morning|good afternoon|good evening|magandang)\b/i', $lq)) {
            return [
                'text' => "Hello! 👋 Ako ang USM Hospital & Pharmacy Store Assistant.\n\nMaaari kitang tulungan sa:\n• Presyo at availability ng mga gamot sa store\n• OPD consultation clinic hours (Mon–Fri, 8 AM–5 PM)\n• Student (10%) at Senior/PWD (20%) discounts sa store\n• Admission at prescription process\n\n(Paalala: Para sa medical checkup o sintomas, mangyaring magpakonsulta sa ating OPD doctors).\n\nAnong gamot o impormasyon ang nais mong itanong?",
                'follow_ups' => ['What medicines are currently in stock?', 'What are the OPD clinic hours?', 'What discounts apply?'],
            ];
        }

        // Clean Fallback (never echoing the raw sentence)
        return [
            'text' => "Hindi ko nahanap ang gamot o katanungan na iyan sa ating Pharmacy Store formulary.\n\nMaaari kang magtanong sa akin tungkol sa:\n• Availability at presyo ng specific na gamot (hal. \"May amoxyclav ba?\", \"Magkano ang Paracetamol?\")\n• OPD clinic hours at lokasyon ng infirmary\n• Student at Senior/PWD discounts sa store\n\nAnong gamot o serbisyo sa ospital ang nais mong itanong?",
            'follow_ups' => ['What medicines are in stock?', 'What are the OPD clinic hours?', 'What discounts apply?'],
        ];
    }

    /**
     * Determine if a medicine is Prescription-Only (Rx) or Over-The-Counter (OTC).
     */
    protected function isRxMedicine(Medicine $medicine): bool
    {
        $rxCategories = ['Antibiotic', 'IV Fluids', 'Antihypertensive', 'Antidiabetic', 'Injectable', 'Corticosteroid'];
        if (in_array($medicine->category, $rxCategories, true)) {
            return true;
        }

        $rxKeywords = ['amoxyclav', 'amoxiclav', 'co-amoxiclav', 'amoxicillin', 'ceftriaxone', 'ampicillin', 'mefenamic', 'salbutamol', 'losartan', 'metformin', 'tramadol', 'ciprofloxacin', 'azithromycin'];
        $combined = strtolower($medicine->name.' '.($medicine->generic_name ?? ''));
        foreach ($rxKeywords as $kw) {
            if (str_contains($combined, $kw)) {
                return true;
            }
        }

        return false;
    }
}
