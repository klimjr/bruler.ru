<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'seo_fields',
        'type',
        'seo_title',
        'route',
        'h1',
        'payload'
    ];

    protected $casts = [
        'seo_fields' => 'array',
        'payload' => 'array'
    ];

    const TYPE_MAIN_PAGE = 'main_page';
    const TYPE_ABOUT_BRAND = 'about_brand';
    const TYPE_COLLECTION = 'collection';
    const TYPE_DOCUMENTS = 'documents';
    const TYPE_PREORDER = 'preorder';
    const TYPE_REFUND = 'refund';
    const TYPE_PAYMENT = 'payment';
    const TYPE_DELIVERY = 'delivery';
    const TYPE_CONTACTS = 'contacts';
    const TYPE_PROFILE = 'profile';
    const TYPE_CART = 'cart';
    const TYPE_LOGIN = 'login';
    const TYPE_PROFILE_PASSWORD_RESET = 'profile-password-reset';
    const TYPE_PROFILE_ORDERS = 'profile-orders';
    const TYPE_REGISTER = 'register';
    const TYPE_PROFILE_FAVOURITES = 'profile-favourites';
    const TYPE_OTHER = 'other';


    const RENDER_TYPE_STATIC_PAGE = 'static_page';
    const RENDER_TYPE_DYNAMIC_PAGE = 'dynamic_page';

    const PAGE_TYPE_PRODUCT = 'product';
    const PAGE_TYPE_CATEGORY = 'category';
    const PAGE_TYPE_LANDING = 'landing';
}
