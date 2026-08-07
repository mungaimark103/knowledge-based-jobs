<?php

namespace App\Notifications;

use App\Models\JobPosting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewJobPostingNotification extends Notification
{
    use Queueable;

    public function __construct(public JobPosting $jobPosting)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $orgName = $this->jobPosting->organization?->name ?? 'Hiring Organization';

        return (new MailMessage)
            ->subject("New Job Match: {$this->jobPosting->title} at {$orgName}")
            ->greeting("Hello {$notifiable->name},")
            ->line("A new job vacancy matching your candidate skill profile has just been posted!")
            ->line("**Job Title:** {$this->jobPosting->title}")
            ->line("**Organization:** {$orgName}")
            ->line("**Location:** {$this->jobPosting->location}")
            ->line("**Grade:** {$this->jobPosting->grade}")
            ->action('View Vacancy & Apply via KBS Wizard', route('opportunities.index', ['search' => $this->jobPosting->title]))
            ->line('Thank you for using the Knowledge-Based Talent & Matching System!');
    }

    public function toArray(object $notifiable): array
    {
        $orgName = $this->jobPosting->organization?->name ?? 'Hiring Organization';

        return [
            'job_id' => $this->jobPosting->id,
            'title' => $this->jobPosting->title,
            'organization_name' => $orgName,
            'grade' => $this->jobPosting->grade,
            'location' => $this->jobPosting->location,
            'message' => "New matching vacancy '{$this->jobPosting->title}' posted by {$orgName}.",
            'url' => route('opportunities.index', ['search' => $this->jobPosting->title]),
        ];
    }
}
