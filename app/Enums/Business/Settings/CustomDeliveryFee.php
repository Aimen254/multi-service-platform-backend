<?php

namespace App\Enums\Business\Settings;

use BenSampo\Enum\Enum;

/**
 * @method static static DeliveryFeeByMileage()
 * @method static static DeliveryFeeAsPercentageOfSale()
 */
final class CustomDeliveryFee extends Enum
{
    const DeliveryFeeByMileage = 0;
    const DeliveryFeeAsPercentageOfSale = 1;
}
