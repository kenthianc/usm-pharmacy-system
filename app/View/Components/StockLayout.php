<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class StockLayout extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $title = 'USM Pharmacy — Inventory & Stock Management',
        public string $active = 'dashboard'
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.stock-layout');
    }
}
