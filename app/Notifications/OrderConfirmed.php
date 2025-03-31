<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\StoreSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Markdown;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmed extends Notification
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
        return ["mail"];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $date = $this->order->created_at->format("d.m.Y");
        $time = $this->order->created_at->format("H:i");
        $order_id = $this->order->id;
        $products = [];
        foreach ($this->order->products as $product) {
            $price = $product["price"] * $product["quantity"];
            $products[] =
                $product["name"] .
                ": " .
                $product["quantity"] .
                " шт. - " .
                $price .
                " руб.";
        }
        $filtered_products = implode("\n", $products);
        $total_price = $this->order->price;
        $storeSettings = StoreSetting::first();
//        $paymentOptions = \Arr::mapWithKeys($storeSettings->events["payments"],fn($payment,$key) => [$key => $payment["label"]]);
        $deliveryOptions = \Arr::mapWithKeys($storeSettings->events["delivery"], fn($delivery, $key) => [$key => $delivery["label"]]);

//        $delivery = Order::DELIVERY_TYPES[$this->order->delivery_type]["label"];
        $delivery = $deliveryOptions[$this->order->delivery_type];

        // TODO: решить проблему с ID и вернуть в письмо
        // $payment_method = Order::PAYMENT_TYPES[$this->order->payment_type - 1]['label'];
        return (new MailMessage())
            ->subject("Твой заказ подтвержден, $this->username")
            ->greeting(Markdown::parse("<span></span>"))
            ->line(
                Markdown::parse(
                    "<p style='margin: 0 auto; max-width: 500px; font-size: 15px'>Привет, $this->username!</p>"
                )
            )
            ->line(
                Markdown::parse(
                    "<p style='margin: 0 auto; max-width: 500px; font-size: 15px'>Мы получили ваш заказ и благодарим за выбор бренда Bruler d'amour.</p>"
                )
            )
            ->line(
                Markdown::parse(
                    "<p style='margin: 0 auto; max-width: 500px; font-size: 20px; font-weight: 600'>Информация о заказе:</p>"
                )
            )
            ->line(Markdown::parse("<ul>"))
            ->line(
                Markdown::parse(
                    "<li style='margin: 0 auto; max-width: 500px; font-size: 15px'>Номер заказа: #$order_id</li>"
                )
            )
            ->line(
                Markdown::parse(
                    "<li style='margin: 0 auto; max-width: 500px; font-size: 15px'>Дата заказа: $date</li>"
                )
            )
            ->line(
                Markdown::parse(
                    "<li style='margin: 0 auto; max-width: 500px; font-size: 15px'>Время заказа: $time</li>"
                )
            )
            ->line(Markdown::parse("</ul>"))
            ->line(
                Markdown::parse(
                    "<p style='margin: 0 auto; max-width: 500px; font-size: 20px; font-weight: 600'>Товары в заказе:</p>"
                )
            )
            ->line(
                Markdown::parse(
                    "<p style='margin: 0 auto; max-width: 500px; font-size: 15px'>$filtered_products</p>"
                )
            )
            ->line(
                Markdown::parse(
                    "<p style='margin: 0 auto; max-width: 500px; font-size: 15px'>Общая Сумма заказа: $total_price руб.</p>"
                )
            )
            // ->line(Markdown::parse("<p style='margin: 0 auto; max-width: 500px; font-size: 15px'>Способ оплаты: $payment_method</p>"))
            ->line(
                Markdown::parse(
                    "<p style='margin: 0 auto; max-width: 500px; font-size: 20px; font-weight: 600'>Информация о доставке:</p>"
                )
            )
            ->line(Markdown::parse("<ul>"))
            ->line(
                Markdown::parse(
                    "<li style='margin: 0 auto; max-width: 500px; font-size: 15px'><span style='font-weight: 600'>Курьерская служба:</span> $delivery</li>"
                )
            )
            ->line(Markdown::parse("</ul>"))
            ->line(
                Markdown::parse(
                    "<p style='margin: 0 auto; max-width: 500px; font-size: 20px; font-weight: 600'>Статус заказа:</p>"
                )
            )
            ->line(
                Markdown::parse(
                    "<p style='margin: 0 auto; max-width: 500px; font-size: 15px'>Если вы оформили заказ из наличия:</p>"
                )
            )
            ->line(Markdown::parse("<ul>"))
            ->line(
                Markdown::parse(
                    "<li style='margin: 0 auto; max-width: 500px; font-size: 15px'>Ваш заказ будет подтвержден, обработан и отправлен в течение 3 рабочих дней. Мы передаем заказы в курьерскую службу с понедельника по пятницу.</li>"
                )
            )
            ->line(
                Markdown::parse(
                    "<li style='margin: 0 auto; max-width: 500px; font-size: 15px'>Мы понимаем, насколько важны сроки доставки, поэтому стараемся передавать заказы в курьерскую службу как можно быстрее:</li>"
                )
            )
            ->line(Markdown::parse("<ul>"))
            ->line(
                Markdown::parse(
                    "<li style='margin: 0 auto; max-width: 500px; font-size: 15px'>Заказы, оформленные до 14:00 с понедельника по пятницу, передаются в курьерскую службу в тот же день.</li>"
                )
            )
            ->line(
                Markdown::parse(
                    "<li style='margin: 0 auto; max-width: 500px; font-size: 15px'>Заказы, оформленные после 14:00, будут переданы на следующий рабочий день.</li>"
                )
            )
            ->line(
                Markdown::parse(
                    "<li style='margin: 0 auto; max-width: 500px; font-size: 15px'>Если вы оформили заказ в пятницу после 14:00, он будет передан в курьерскую службу в понедельник.</li>"
                )
            )
            ->line(Markdown::parse("</ul>"))
            ->line(
                Markdown::parse(
                    "<li style='margin: 0 auto; max-width: 500px; font-size: 15px'>Вы можете отслеживать статус вашего заказа в личном кабинете СДЭК.</li>"
                )
            )
            ->line(
                Markdown::parse(
                    "<li style='margin: 0 auto; max-width: 500px; font-size: 15px; font-weight: 600'>Мы направим информацию с трек-номером  в WhatsApp</li>"
                )
            )
            ->line(Markdown::parse("</ul>"))
            ->line(
                Markdown::parse(
                    "<p style='margin: 0 auto; max-width: 500px; font-size: 15px'>Если вы оформили предзаказ:</p>"
                )
            )
            ->line(Markdown::parse("<ul>"))
            ->line(
                Markdown::parse(
                    "<li style='margin: 0 auto; max-width: 500px; font-size: 15px'>Мы свяжемся с вами в WhatsApp и пришлем трек-номер для отслеживания после поступления изделий.</li>"
                )
            )
            ->line(Markdown::parse("</ul>"))
            ->line(
                Markdown::parse(
                    "<p style='margin: 0 auto; max-width: 500px; font-size: 15px'>Мы уверены, что вы оцените наше внимание к деталям и качество наших изделий. Если у вас возникнут вопросы или потребуется помощь, обращайтесь в нашу службу поддержки https://t.me/bruler_support . Мы работаем с понедельника по пятницу с 10:00 до 19:00.</p>"
                )
            )
            ->line(
                Markdown::parse(
                    "<p style='margin: 0 auto; max-width: 500px; font-size: 15px'>С нетерпением ждем, чтобы вы насладились своими покупками в <span style='font-weight: 600'>Bruler</span>!</p>"
                )
            )
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
