<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Markdown;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class CertificateNotificataion extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $code, public $image)
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
        $vk_img = asset('images/mail/vk-img.png');
        $tg_img = asset('images/mail/tg-img.png');
        $mail_img = asset('images/mail/mail-img.png');
        return (new MailMessage)
            ->subject('Добро пожаловать в мир Bruler d’amour')
            ->greeting(Markdown::parse("<img alt='header_image' src='$imageHeaderPath'/>"))
            ->line(Markdown::parse("<p style='font-weight: 700; text-align: center; font-size: 20px'>Подарочный сертификат Bruler d’amour</p>"))
            ->line(Markdown::parse("<p style='margin: 0 auto; max-width: 500px; font-size: 15px'>Твой сертификат: $this->code</p>"))
            ->line(Markdown::parse("<p style='margin: 0 auto; max-width: 500px; font-size: 15px'>Используй его на сайте <a href='https://bruler.ru'>https://bruler.ru</a></p>"))
            ->line(Markdown::parse("<p style='margin: 0 auto; max-width: 500px; font-size: 15px'><img style='margin: 25px auto; width: 100%;' alt='arrow_right' src='$this->image'/></p>"))
            ->line(Markdown::parse("<p style='margin: 0 auto; max-width: 500px; font-size: 15px'>Если стоимость услуги больше стоимости номинала, возможна доплата со стороны держателя сертификата. Если стоимость покупки меньше номинала сертификата, остаток можно использовать в счет следующей покупки</p>"))
            ->line(Markdown::parse("<p style='margin: 0 auto; max-width: 500px; font-size: 15px'>Подарочный сертификат не является именным и действует “на предъявителя”</p>"))
            ->line(Markdown::parse("<p style='margin: 0 auto; max-width: 500px; font-size: 15px'>Для использования сертификата необходимо зайти на сайт <a href='https://bruler.ru'>https://bruler.ru</a>, выбрать позиции для покупки, в корзине в окне “номер сертификата” ввести номер из письма</p>"))
            ->line(Markdown::parse("<p style='margin: 20px auto 20px auto; max-width: 500px; font-size: 15px'><b>Служба поддержки</b><b style='margin-left: 50px'>Соцсети</b></p>"))
            ->line(Markdown::parse("<p style='margin: 0 auto; max-width: 500px; font-size: 15px'><a href='support@bruler.ru'><img alt='mail_img' src='$mail_img'/></a><a style='margin-left: 15px' href='https://t.me/bruler_support'><img alt='tg_img' src='$tg_img'/></a><a style='margin-left: 110px' href='https://t.me/bruler_support'><img alt='vk_img' src='$vk_img'/></a><a style='margin-left: 15px' href='https://t.me/bruler_support'><img alt='tg_img' src='$tg_img'/></a></p>"))
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
