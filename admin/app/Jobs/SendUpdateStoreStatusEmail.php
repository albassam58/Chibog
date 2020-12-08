<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendUpdateStoreStatusEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $to;
    protected $cc;
    protected $bcc;
    protected $details;

    // queue options
    public $timeout = 7200; // 2 hours
    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($to, $details, $cc = null, $bcc = null)
    {
        $this->to = $to;
        $this->cc = $cc;
        $this->bcc = $bcc;
        $this->details = $details;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $details = $this->details;

        Mail::send('emails.update-store-status', $details, function($message) use($details) {
            $message = $message->to($this->to);
            
            // with cc
            if ($this->cc) {
                $message = $message->cc($this->cc);
            }
            // with bcc
            if ($this->bcc) {
                $message = $message->bcc($this->bcc);
            }

            $message->subject($details['subject']);
        });
    }
}
