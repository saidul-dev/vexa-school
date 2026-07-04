<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SuperAdminAproved extends Mailable
{
    use Queueable, SerializesModels;

    public $subscriptionsmail;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subscriptionsmail)
    {
        $this->subscriptionsmail = $subscriptionsmail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Subscription Report')->view('email.superAdminAproved')->from(get_settings('smtp_user'),get_settings('system_title'));
    }
}
