<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentsController extends Controller
{
    public function create()
    {
        return view('payments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'amount' => 'required|integer|min:100'
        ]);
        $request->user()->payments()->create([
            'amount' => $request->amount,
            'email' => $request->email,
            'currency' => $request->currency,
            'name' => $request->name,
            'description' => $request->description,
            'message' => $request->message,
            'code' => (string) Str::uuid(),
        ]);
//        $payment = Payment::create([]);
    }
}
