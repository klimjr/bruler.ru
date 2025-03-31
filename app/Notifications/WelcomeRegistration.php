<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Markdown;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class WelcomeRegistration extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $username, public $email)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $imageHeaderPath = asset('images/mail/mail_header_img.png');
        $imageArrowRightPath = asset('images/mail/arrow_right.png');
        $products = Product::take(3)->get();

        $images = $products->map(function (Product $product) {
            return "<a href=\"" . $product->getRouteUrl() . "\" style=\"display: inline-block; width: 32%; box-sizing: border-box; text-decoration: none !important; color: black !important; vertical-align: top !important; text-align: center;\"><img height=\"180px\" src=\"" . $product->getImageUrlAttribute() . "\" alt=\"" . $product->name . "\"><p style='text-align: center !important; font-size: 8px !important; font-weight: 400'>$product->name</p><p style='text-align: center !important; font-size: 8px !important; font-weight: 700; margin-top: -5px'>$product->price ₽</p></a>";
        })->implode(' ');

        return (new MailMessage)
            ->subject('Добро пожаловать в мир Bruler d’amour')
            ->greeting(Markdown::parse("<img alt='header_image' src='$imageHeaderPath'/>"))
            ->line(Markdown::parse("<p style='font-weight: 700; text-align: center; font-size: 20px'>Добро пожаловать в мир Bruler d’amour</p>"))
            ->line(Markdown::parse("<p style='margin: 0 auto; max-width: 500px; font-size: 15px'>Привет $this->username!</p>"))
            ->line(Markdown::parse("<p style='margin: 15px auto 0 auto; max-width: 500px; font-size: 15px'>Добро пожаловать в Bruler d’amour  – место, где дерзкий стиль переплетается с искренностью и современностью. Мы рады видеть тебя в нашем сообществе творческих и стильных личностей.</p>"))
            ->line(Markdown::parse("<p style='font-weight: 700; text-align: center; font-size: 20px; margin: 30px 0'>Твой логин: $this->email</p>"))
            ->line(Markdown::parse("<p style='margin: 15px auto 0 auto; max-width: 500px; font-size: 15px'>С этого момента у тебя есть доступ к личному кабинету, где ты сможешь наслаждаться уникальными предложениями и следить за последними трендами.</p>"))
            ->line(Markdown::parse("<p style='margin: 15px auto 0 auto; max-width: 500px; font-size: 15px'>Будь в курсе последних новинок и акций, следи за нашими обновлениями, которые мы будем отправлять на этот адрес электронной почты. Но, конечно, если ты решишься отказаться от рассылок, всегда сможешь это сделать, кликнув по соответствующей кнопке в наших письмах.</p>"))
            ->line(Markdown::parse("<p style='margin: 15px auto 0 auto; max-width: 500px; font-size: 15px'>Дерзай, будь искренним и оставайся в тренде!</p>"))
            ->line(Markdown::parse("<div style='margin: 25px auto 0 auto; max-width: 500px; font-size: 19px; text-align: right; padding-right: 15px'><a href='https://bruler.ru/collection' style='color: black !important; text-decoration: none'>собери образ с bruler</a><img style='margin: 10px 0 auto 10px' width='24' height='15' alt='arrow_right' src='$imageArrowRightPath'/></div>"))
            ->line(Markdown::parse("<div style='margin: 15px auto 0 auto; max-width: 500px; font-size: 19px;'>$images</div>"))
            ->line(Markdown::parse("<p style='text-align: center; font-size: 15px; margin: 30px 0'><span style='margin-right: 20px'>Служба поддержки: </span><span style='word-spacing: 10px'>[Телефон] support@bruler.ru</span></p>"))
            ->salutation(Markdown::parse("<p></p>"));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
