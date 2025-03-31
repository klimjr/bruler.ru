<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Mail\Markdown;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RecoveryCodeNotification extends Notification
{

  public $code;

  public function __construct($code)
  {
    $this->code = $code;
  }

  public function toMail(User $notification)
  {
    $recovery_code = $this->code ?? 'not_found';
    return (new MailMessage)
      ->subject('Код подтверждения')
        ->greeting(Markdown::parse("<p></p>"))
      ->line(Markdown::parse("<p style='margin: 0 auto 10px auto; max-width: 500px; font-size: 15px'>Код для подтверждения регистрации: <span style='font-weight: 700'>$recovery_code</span></p>"))
      ->line(Markdown::parse("<p style='margin: 0 auto; max-width: 500px; font-size: 15px'>Если вы не запрашивали данный код, то просто проигнорируйте данное сообщение</p>"))
      ->salutation(Markdown::parse("<p></p>"));
  }

  public function via($notifiable)
  {
    return ['mail'];
  }
}
