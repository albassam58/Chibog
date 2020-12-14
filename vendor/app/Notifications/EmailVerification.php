<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class EmailVerification extends Notification implements ShouldQueue
{
    use Queueable;

    public $vendor;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($vendor)
    {
        $this->queue = "emails";
        $this->vendor = $vendor;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $temporarySignedUrl = URL::temporarySignedRoute(
            'vendor.verify', Carbon::now()->addMinutes(60), ['id' => $this->vendor['id']]
        );

        $url = config('app.url') . "/verification?email=" . $this->vendor['email'] . "&queryUrl=" . urlencode($temporarySignedUrl);

        return (new MailMessage)
                    ->subject(Lang::get('Email Verification'))
                    ->line(Lang::get('You are receiving this email because we received an email verification request for your account.'))
                    ->action(Lang::get('Verify Email'), $url)
                    ->line(Lang::get('This email verificationlink will expire in 60 minutes.'))
                    ->line(Lang::get('If you did not request an email verification, no further action is required.'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
