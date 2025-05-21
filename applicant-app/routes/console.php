<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('tickets:delete-all')
    ->dailyAt('00:00')
    ->timezone('Asia/Manila');
