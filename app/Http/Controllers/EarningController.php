<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Earning;

class EarningController extends Controller
{
    public function index()
    {
        $earnings = Earning::all();
        return response()->json($earnings);
    }

    public function show(Earning $earning)
    {
        return response()->json($earning);
    }

}
