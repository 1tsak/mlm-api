<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Withdrawal;

class AdminWithdrawalController extends Controller
{
    public function listWithdrawalRequests()
    {
        // Fetch only pending withdrawals along with user and bank account details
        $withdrawals = Withdrawal::with(['user.bankAccount'])
            ->where('status', 'pending')
            ->get();

        return response()->json($withdrawals, 200);
    }

    public function updateWithdrawalRequestStatus(Request $request, Withdrawal $withdrawal)
    {
        $request->validate([
            'status' => 'required|in:approved,declined'
        ]);

        $withdrawal->status = $request->status;
        $withdrawal->save();

        if ($request->status == 'declined') {
            // Return the amount back to the user's balance
            $user = $withdrawal->user;
            $user->balance += $withdrawal->amount;
            $user->save();
        }

        return response()->json(['message' => 'Withdrawal request status updated', 'withdrawal' => $withdrawal], 200);
    }
}
