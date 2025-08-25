<?php

namespace App\Enum;

enum ProductFilterEnum: string
{
    case PRICES = 'prices';
    case CATEGORIES = 'categories';
    case PRICE_MIN = 'price_min';
    case PRICE_MAX = 'price_max';
    case SEARCH = 'search';

}
