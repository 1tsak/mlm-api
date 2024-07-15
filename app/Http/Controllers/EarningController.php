<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Earning;
use Illuminate\Support\Facades\Auth;

class EarningController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $earnings = $user->earnings()->get();

        return response()->json($earnings);
    }

    public function show(Earning $earning)
    {
        return response()->json($earning);
    }

}
