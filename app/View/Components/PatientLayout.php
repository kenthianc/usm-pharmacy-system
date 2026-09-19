<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class PatientLayout extends Component
{
    /**
     * The active navigation item.
     */
    public string $active;

    /**
     * Create a new component instance.
     */
    public function __construct(string $active = 'dashboard')
    {
        $this->active = $active;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.patient', ['active' => $this->active]);
    }
}
