<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [10, 30, 60];
    public $timeout = 30;

    protected $to;
    protected $subject;
    protected $view;
    protected $data;

    public function __construct($to, $subject, $view, $data = [])
    {
        $this->to = $to;
        $this->subject = $subject;
        $this->view = $view;
        $this->data = $data;
    }

    public function handle()
    {
        try {
            Mail::send($this->view, $this->data, function ($message) {
                $message->to($this->to)
                    ->subject($this->subject);
            });

            Log::info('Email sent successfully', [
                'to' => $this->to,
                'subject' => $this->subject
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send email', [
                'to' => $this->to,
                'subject' => $this->subject,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('Email job failed after retries', [
            'to' => $this->to,
            'subject' => $this->subject,
            'error' => $exception->getMessage()
        ]);
    }
}
