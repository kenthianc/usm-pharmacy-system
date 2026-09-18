<?php

namespace App\Providers;

use App\Models\Medicine;
use App\Models\Patient;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
        });

        View::composer('prescriptions.partials.modal-create', function ($view) {
            $data = $view->getData();
            if (! isset($data['patients'])) {
                $view->with('patients', Patient::with('user')->get()->sortBy('name'));
            }
            if (! isset($data['medicines'])) {
                $medicines = Medicine::with('stockBatches')->get()->map(function ($medicine) {
                    return [
                        'id' => $medicine->id,
                        'name' => $medicine->name,
                        'generic_name' => $medicine->generic_name,
                        'unit' => $medicine->unit,
                        'unit_price' => (float) $medicine->unit_price,
                        'available_stock' => $medicine->available_stock,
                    ];
                });
                $view->with('medicines', $medicines);
            }
        });
    }
}
