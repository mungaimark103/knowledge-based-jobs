<?php

namespace App\Mail;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public JobApplication $application)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Application Confirmation - ' . $this->application->jobPosting->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: "
                <h2>Application Confirmation</h2>
                <p>Dear {$this->application->candidate->name},</p>
                <p>Your application for <strong>{$this->application->jobPosting->title}</strong> at <strong>{$this->application->jobPosting->organization->name}</strong> has been successfully submitted and evaluated against our Knowledge-Based System criteria.</p>
                <p>Application ID: #{$this->application->id}</p>
                <p>Date Submitted: " . \Carbon\Carbon::parse($this->application->created_at)->format('M d, Y H:i') . " UTC</p>
                <p>Thank you for using Impact Talent KBS System.</p>
            ",
        );
    }
}
