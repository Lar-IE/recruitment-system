<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    /**
     * When true, the slot is rendered full width (no max-w wrapper).
     * Used by login and register for split-screen layout.
     */
    public bool $fullWidth = false;

    /**
     * Create the component instance.
     */
    public function __construct(bool $fullWidth = false)
    {
        $this->fullWidth = $fullWidth;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.guest');
    }
}
