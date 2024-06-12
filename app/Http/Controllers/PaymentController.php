<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentStoreRequest;
use App\Models\Transaction;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = auth()->user()->transactions()->paginate(10);

        return response()->json(
            [
                'status' => 'OK',
                'data' => $transactions
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PaymentStoreRequest $request)
    {
        $transaction = new Transaction();
        $transaction->user_id = auth()->user()->id;
        $transaction->amount = $request->amount;
        $transaction->status = 'pending';
        $transaction->save();

        return response()
            ->json(
            [
                'status' => 'OK',
                'data' => []
            ]
            )->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
