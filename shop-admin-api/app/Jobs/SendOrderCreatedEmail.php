<?php

namespace App\Jobs;

use App\Mail\OrderCreated;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOrderCreatedEmail implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    /**
     * @var Order
     */
    public $order;

    /**
     * Create a new job instance.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        // serialize order for the queue (the model will be re‑hydrated automatically)
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // make sure we have the associated user email available
        $user = $this->order->user ?? null;
        if ($user && $user->email) {
            Mail::to($user->email)->send(new OrderCreated($this->order));
        }
    }
}
