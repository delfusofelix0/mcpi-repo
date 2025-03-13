<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\WorkPosition;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $queryReg = Registration::select([
            'id',
            'first_name',
            'last_name',
            'email',
            'phone',
            'highest_education',
            'work_position_id',
            'created_at',
            'status',
            'remarks',
            'remarks_date'
        ])->with(['position' => function($query) {
            $query->withTrashed()->select('id', 'name');
        }])->latest();
        $queryPos = WorkPosition::latest();

        $registrations = $queryReg->get();
        $positions = $queryPos->get();

        return Inertia::render('Dashboard', [
            'registrations' => $registrations,
            'positions' => $positions
        ]);
    }
}
