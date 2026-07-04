<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudentsEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $studentFeeManager;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($studentFeeManager)
    {
        $this->studentFeeManager = $studentFeeManager;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Purchase Report')->view('email.studentsEmail')->from(get_settings('smtp_user'),get_settings('system_title'));
    }
}
