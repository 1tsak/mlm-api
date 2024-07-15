<?php

// app/Http/Controllers/BankAccountController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BankAccount;
use Illuminate\Support\Facades\Auth;

class BankAccountController extends Controller
{
    public function getBankAccount()
    {
        $user = Auth::user();
        $bankAccount = $user->bankAccount;

        return response()->json($bankAccount, 200);
    }

    public function updateBankAccount(Request $request)
    {
        $request->validate([
            'accName' => 'required|string|max:255',
            'accNumber' => 'required|string|max:255',
            'bankName' => 'required|string|max:255',
            'ifsc' => 'required|string|max:255',
            'bankBranch' => 'required|string|max:255',
        ]);

        $user = Auth::user();

        $bankAccount = BankAccount::updateOrCreate(
            ['user_id' => $user->id],
            [
                'accName' => $request->accName,
                'accNumber' => $request->accNumber,
                'bankName' => $request->bankName,
                'ifsc' => $request->ifsc,
                'bankBranch' => $request->bankBranch,
            ]
        );

        return response()->json(['message' => 'Bank account details saved successfully', 'bank_account' => $bankAccount], 200);
   
    }
}
