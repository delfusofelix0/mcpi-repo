<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Registration;
use Illuminate\View\View;

class ViewApplicant extends Component
{
    public $applicant;

    public function __construct()
    {
        // Assuming you want to show the logged-in user's application
        // You might need to adjust this logic based on your requirements
//        $this->applicant = Registration::where('user_id', auth()->id())->first();
    }

    public function render(): View
    {
        return view('view-applicant');
    }
}
