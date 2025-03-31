<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderSentToDelivery extends Notification
{

    /**
     * Create a new notification instance.
     */
    public function __construct(public $username, public Order $order)
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
        $date = $this->order->delivery_date;
        $time = $this->order->delivery_time;
        $delivery = Order::DELIVERY_TYPES[$this->order->delivery_type]['label'];
        $track_number = $this->order->track_number;
        return (new MailMessage)
            ->subject("Твой заказ в пути – Bruler d’amour")
            ->line("Привет, $this->username,")
            ->line("Твои вещи из Bruler уже в пути! Мы готовимся доставить стиль прямо к тебе.")
            ->line("Детали доставки:
Дата доставки: $date
Время доставки: $time

Курьерская служба: $delivery
Трек-номер: $track_number
")
            ->line("Если у тебя возникнут вопросы или потребуется помощь, обращайся к нашей службе поддержки.")
            ->line("Спасибо, что выбрал(а) нас. Уверены, твой заказ Bruler d’amour принесет тебе массу удовольствия!");
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
