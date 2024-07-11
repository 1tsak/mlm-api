<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Task;
use App\Models\UserTask;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function userDetails()
    {
        $user = Auth::user();
        return response()->json($user, 200);
    }

    public function directReferrals()
    {
        $user = Auth::user();
        return response()->json([
            'direct_referrals_count' => $user->referrals()->count(),
            'direct_referrals' => $user->referrals
        ]);
    }

    public function allLevelReferrals()
    {
        $user = Auth::user();
        $allReferrals = $user->getAllReferrals();
        return response()->json([
            'all_level_referrals_count' => $allReferrals->count(),
            'all_level_referrals' => $allReferrals
        ]);
    }

    public function userEarnings()
    {
        $user = Auth::user();
        $taskEarnings = $user->tasks()->sum('reward');
        $levelEarnings = $user->earnings()->sum('amount');
        $totalEarnings = $taskEarnings + $levelEarnings;

        return response()->json([
            'task_earnings' => $taskEarnings,
            'level_earnings' => $levelEarnings,
            'total_earnings' => $totalEarnings
        ]);
    }

    public function createWithdrawalRequest(Request $request)
    {
        $user = Auth::user();
        $amount = $request->amount;

        if ($user->balance < $amount) {
            return response()->json(['error' => 'Insufficient balance'], 400);
        }

        $user->balance -= $amount;
        $user->save();

        $user->withdrawals()->create([
            'amount' => $amount,
            'status' => 'pending'
        ]);

        return response()->json(['message' => 'Withdrawal request created'], 201);
    }

    public function getWithdrawals()
    {
        $user = Auth::user();
        $withdrawals = $user->withdrawals;

        return response()->json($withdrawals, 200);
    }
    public function getUserNameBySponsorId($sponsor_id)
    {
        $user = User::where('sponsor_id', $sponsor_id)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json(['name' => $user->name], 200);
    }
    public function getStats()
    {
        $user = Auth::user();
        $totalUsers = User::count();
        $directReferralsCount = $user->referrals()->count();
        $allLevelReferralsCount = $user->getAllReferrals()->count();
        $taskEarnings = $user->earnings()->sum('amount');
        $levelEarnings = $user->getAllReferrals()->sum(function ($referral) {
            return $referral->earnings()->sum('amount');
        });

        return response()->json([
            'total_users' => $totalUsers,
            'direct_referrals_count' => $directReferralsCount,
            'all_level_referrals_count' => $allLevelReferralsCount,
            'task_earnings' => $taskEarnings,
            'level_earnings' => $levelEarnings,
            'total_earnings' => $taskEarnings + $levelEarnings,
        ]);
    }
}
