<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static inStock()
 * @method static static outOfStock()
 */
final class ProductStockStatus extends Enum
{
    const inStock = 'in_stock';
    const outOfStock = 'out_of_stock';
}
