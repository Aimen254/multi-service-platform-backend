<?php

namespace App\Enums\Business\Settings;

use BenSampo\Enum\Enum;

/**
 * @method static NoDelivery()
 * @method static PlatFormDelivery()
 * @method static SelfDelivery()
 */
final class DeliveryType extends Enum
{
    const NoDelivery = 0;
    const PlatformDelivery = 1;
    const SelfDelivery = 2;
}
