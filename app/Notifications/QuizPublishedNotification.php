<?php

namespace App\Notifications;

use App\Models\Quiz;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuizPublishedNotification extends Notification
{
    protected $quiz;

    public function __construct(Quiz $quiz)
    {
        $this->quiz = $quiz;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Yeni Test Dərc Edildi')
            ->line('Yeni test dərc edildi: ' . $this->quiz->title)
            ->line('Kateqoriya: ' . $this->quiz->category->name)
            ->action('Testə Bax', url('/quizzes/' . $this->quiz->id))
            ->line('Uğurlar diləyirik!');
    }
}

