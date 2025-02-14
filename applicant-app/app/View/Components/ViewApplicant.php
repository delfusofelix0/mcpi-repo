<?php

namespace App\View\Components;

use App\Models\Position;
use Illuminate\View\Component;
use Illuminate\View\View;

class ViewApplicant extends Component
{
    public $applicant;

    public function __construct($id)
    {
        $this->applicant = Position::findOrFail($id);
    }

    public function render(): View
    {
        return view('view-applicant');
    }
}
