<?php

namespace App\Jobs;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PaymentUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $transaction;
    public $amount;
    public $status;

    /**
     * Create a new job instance.
     */
    public function __construct(Transaction $transaction, int $amount, string $status)
    {
        $this->transaction = $transaction;
        $this->amount = $amount;
        $this->status = $status;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->transaction->amount = $this->amount;
        $this->transaction->status = $this->status;
        $this->transaction->save();
    }
}
