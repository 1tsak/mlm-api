<?php

namespace App\Http\Controllers;

use App\Models\Earning;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Task;
use App\Models\UserTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

        if (!$user->bankAccount) {
            return response()->json(['error' => 'Bank account details are required'], 400);
        }

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

        // Calculate earnings
        $taskEarnings = $user->earnings()->where('description', 'ad')->sum('amount');
        $levelEarnings = $user->earnings()->where('description', 'level')->sum('amount');
        $directIncome = $user->earnings()->where('description', 'direct')->sum('amount');
        $telegramIncome = $user->earnings()->where('description', 'telegram')->sum('amount');
        $totalEarnings = $taskEarnings + $levelEarnings + $directIncome;

        // Calculate total withdrawals
        $totalWithdrawals = $user->withdrawals()->where('status', 'approved')->sum('amount');

        return response()->json([
            'total_users' => $totalUsers,
            'direct_referrals_count' => $directReferralsCount,
            'all_level_referrals_count' => $allLevelReferralsCount,
            'task_earnings' => $taskEarnings,
            'level_earnings' => $levelEarnings,
            'direct_income_amount' => $directIncome,
            'total_withdrawal_amount' => $totalWithdrawals,
            'total_earnings' => $totalEarnings,
            'telegram_income' => $telegramIncome,
        ]);
    }
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['error' => 'Current password does not match'], 400);
        }

        $user->password = bcrypt($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password updated successfully'], 200);
    }
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'dob' => 'required|date',
            'address' => 'required|string|max:255',
        ]);

        $user->dob = $request->dob;
        $user->address = $request->address;
        $user->save();

        return response()->json(['message' => 'Profile updated successfully'], 200);
    }
    public function telegramShare(Request $request)
    {
        $user = $request->user();

        $telegramShareCount = Earning::where('user_id', $user->id)
            ->where('description', 'telegram')
            ->count();

        if ($telegramShareCount >= 5) {
            return response()->json([
                'message' => 'You have reached the maximum limit of 5 telegram shares.',
            ], 403);
        }

        $user->balance += 50;
        $user->save();

        Earning::create([
            'user_id' => $user->id,
            'amount' => 50,
            'description' => 'telegram',
        ]);

        return response()->json([
            'message' => 'Balance updated and earning record added.',
            'balance' => $user->balance,
        ], 200);
    }
}
