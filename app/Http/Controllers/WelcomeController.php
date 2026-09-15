<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    /**
     * Display the public landing page (hero + chatbot + features).
     */
    public function index(): View
    {
        $medicines = $this->mappedMedicines();

        return view('welcome', compact('medicines'));
    }

    /**
     * Display the medicine storefront page.
     */
    public function medicines(): View
    {
        $medicines = $this->mappedMedicines();

        return view('medicines', compact('medicines'));
    }

    /**
     * Return a mapped collection of medicines with stock info.
     *
     * @return Collection<int, array<string, mixed>>
     */
    private function mappedMedicines(): Collection
    {
        return Medicine::with('stockBatches')
            ->orderBy('name')
            ->get()
            ->map(function (Medicine $medicine) {
                return [
                    'id' => $medicine->id,
                    'name' => $medicine->name,
                    'generic_name' => $medicine->generic_name,
                    'category' => $medicine->category,
                    'unit' => $medicine->unit,
                    'unit_price' => (float) $medicine->unit_price,
                    'available_stock' => $medicine->available_stock,
                ];
            });
    }
}
