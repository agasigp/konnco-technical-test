<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Http\Requests\PaymentStoreRequest;
use App\Http\Requests\PaymentUpdateRequest;
use App\Jobs\PaymentUpdate;

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
     * Update the specified resource in storage.
     */
    public function update(PaymentUpdateRequest $request, Transaction $transaction)
    {
        PaymentUpdate::dispatch(
            $transaction,
            $request->amount,
            $request->status
        );

        return response()
            ->json(
                [
                    'status' => 'OK',
                    'data' => []
                ]
            );
    }

    public function summary()
    {
        $totalTransaction = Transaction::query()
            ->where('user_id', auth()->user()->id)
            ->count();
        $avgTransaction = (float) Transaction::query()
            ->where('user_id', auth()->user()->id)
            ->avg('amount');
        $highestTransaction = Transaction::query()
            ->where('user_id', auth()->user()->id)
            ->orderBy('amount', 'DESC')
            ->first();
        $lowestTransaction = Transaction::query()
            ->where('user_id', auth()->user()->id)
            ->orderBy('amount', 'ASC')
            ->first();
        $pendingTransaction = Transaction::query()
            ->where('user_id', auth()->user()->id)
            ->where('status', 'pending')
            ->count();
        $completedTransaction = Transaction::query()
            ->where('user_id', auth()->user()->id)
            ->where('status', 'completed')
            ->count();
        $failedTransaction = Transaction::query()
            ->where('user_id', auth()->user()->id)
            ->where('status', 'failed')
            ->count();

        return response()->json([
            'status' => 'OK',
            'data' => [
                "total_transactions" => $totalTransaction,
                "average_amount" => round($avgTransaction, 2),
                "highest_transaction" => $highestTransaction,
                "lowest_transaction" => $lowestTransaction,
                "status_distribution" => [
                    "pending" => $pendingTransaction,
                    "completed" => $completedTransaction,
                    "failed" => $failedTransaction,
                ],
            ]
        ]);
    }
}
