<?php

namespace App\Notifications;

use App\Models\QuizAttempt;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuizCompletedNotification extends Notification
{
    protected $attempt;

    public function __construct(QuizAttempt $attempt)
    {
        $this->attempt = $attempt;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Test Tamamlandı')
            ->line('Test tamamlandı: ' . $this->attempt->quiz->title)
            ->line('Nəticə: ' . $this->attempt->score . ' xal')
            ->line('Faiz: ' . $this->attempt->percentage . '%')
            ->action('Nəticələrə Bax', url('/attempts/' . $this->attempt->id . '/results'))
            ->line('Təbriklər!');
    }
}
