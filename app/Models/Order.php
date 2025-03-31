<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Order extends Model implements Auditable
{
    use SoftDeletes, \OwenIt\Auditing\Auditable;

    const DELIVERY_TYPE_CDEK = 0; // Доставка до двери CDEK
    const DELIVERY_TYPE_CDEK_PVZ = 1; // CDEKPVZ
    const DELIVERY_TYPE_PICKUP = 2; // Самовывоз
    const DELIVERY_TYPE_BOXBERRY = 3; // Boxberry

    const STATUS_NOT_CONFIRMED = 'not_cofirmed';
    const STATUS_PAID_RECEIPT = 'paid_receipt';
    const STATUS_CREATED = 'created';
    const STATUS_CONFIRMED = 'confirmed';

    const STATUS_SHIPPING = 'shipping';
    const STATUS_PAID = 'paid';

    const STATUS_SENT_TO_DELIVERY = 'sent_to_delivery';

    const STATUS_DELIVERED = 'delivered';


    const PAYMENT_STATUS_CREATED = 'created';
    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_REJECTED = 'rejected';

    const PAYMENT_TYPE_CARD = 1;
    // Долями
    const PAYMENT_TYPE_DOLYAMI = 2;
    // Налик
    const PAYMENT_TYPE_CASH = 3;
    const PAYMENT_TYPE_CRYPTO = 4;
    const PAYMENT_TYPE_YANDEX = 5;

    // Долями статусы до
    const DOLYAMI_STATUS_CANCEL = 'cancel';
    const DOLYAMI_STATUS_COMMIT = 'commit';
    const DOLYAMI_STATUS_SUCCESS = 'success';
    const DOLYAMI_STATUS_FAIL = 'fail';
    const DOLYAMI_STATUS_CREATE = 'create';


    // Долями статусы после
    const DOLYAMI_STATUS_NEW = 'new';
    const DOLYAMI_STATUS_REJECTED = 'rejected';
    const DOLYAMI_STATUS_CANCELED = 'canceled';
    const DOLYAMI_STATUS_APPROVED = 'approved';
    const DOLYAMI_STATUS_COMMITED = 'committed';
    const DOLYAMI_STATUS_COMPLETED = 'completed';
    const DOLYAMI_STATUS_WAIT_FOR_COMMIT = 'wait_for_commit';
    const DOLYAMI_STATUS_WAITING_FOR_COMMIT = 'waiting_for_commit';


    const DELIVERY_TYPES = [
        ['id' => \App\Models\Order::DELIVERY_TYPE_CDEK, 'label' => 'курьерская доставка СДЭК', 'description' => 'Доставка до двери по России', 'price' => 0],
        ['id' => \App\Models\Order::DELIVERY_TYPE_CDEK_PVZ, 'label' => 'СДЭК (ПВЗ)', 'description' => 'Доставка до пункта выдачи по России', 'price' => 0],
        [
            'id' => \App\Models\Order::DELIVERY_TYPE_PICKUP,
            'label' => 'самовывоз',
            'description' => 'По адресу: 2-я Бауманская ул.,9/23c3 (БЦ Суперметалл) 3 этаж офис 3306<br>
Контакт: +7 (800)222-13-66<br>
Просьба связаться за 30 минут до визита.<br><br>
График с 10:00 до 19:00<br>
Сб, Вс - выходной',
            'price' => 0
        ],
        ['id' => \App\Models\Order::DELIVERY_TYPE_BOXBERRY, 'label' => 'Boxberry', 'description' => 'Доставка до пункта выдачи по России', 'price' => 0]
    ];

    const PAYMENT_TYPES = [
        ['id' => Order::PAYMENT_TYPE_CARD, 'label' => 'оплата картой онлайн', 'description' => ''],
        //        ['id' => Order::PAYMENT_TYPE_DOLYAMI, 'label' => 'долями', 'description' => 'Оплата заказа до 30 000 рублей делится на 4 платежа по 25% от стоимости. Первый платеж производится сразу, остальные три части будут списываться каждые две недели'],
        ['id' => Order::PAYMENT_TYPE_DOLYAMI, 'label' => 'долями', 'description' => ''],
        ['id' => Order::PAYMENT_TYPE_CRYPTO, 'label' => 'крипта', 'description' => ''],
        ['id' => Order::PAYMENT_TYPE_CASH, 'label' => 'наличными', 'description' => ''],
        ['id' => Order::PAYMENT_TYPE_YANDEX, 'label' => 'Yandex Pay', 'description' => '']
    ];

    protected $fillable = [
        'user_id',
        'products',
        'country',
        'city',
        'city_code',
        'address',
        'index',
        'promocode',
        'price_with_promocode',
        'price_order',
        'delivery_type',
        'comment',
        'recipient_name',
        'recipient_last_name',
        'recipient_phone',
        'recipient_email',
        'payment_type',
        'price',
        'delivery_price',
        'status',
        'delivery_info',
        'payment_status',
        'payment_id',
        'paid_at',
        'confirmation_at',
        'sent_at',
        'close_at',
        'delivery_date',
        'delivery_time',
        'track_number',
        'payment_url',
        'payment_schedule',
        'target_email',
        'certificate',
        'use_certificate',
        'cert_amount',
        'use_bonus',
        'points_amount',
        'type',
    ];

    protected $casts = [
        'products' => 'array',
        'delivery_info' => 'json',
        'certificate' => 'json',
        'paid_at' => 'datetime',
        'payment_schedule' => 'array',
    ];

    protected $auditInclude = [
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getRouteUrl()
    {
        return route('profile.orders.order', ['order' => $this->id]);
    }
}
