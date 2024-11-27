<?php

namespace App\Jobs;

use App\Mail\NewBlogEmail;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNewBlogEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    protected $subscriber;
    protected $post;

    /**
     * Create a new job instance.
     */
    public function __construct($subscriber, $post)
    {
        $this->subscriber = $subscriber;
        $this->post = $post;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->subscriber->email)->send(new NewBlogEmail($this->subscriber, $this->post));
    }
}
