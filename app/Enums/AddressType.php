<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static BILLING()
 * @method static static SHIPPING()
 * @method static static NEWSPAPER()
 */
final class AddressType extends Enum
{
    const BILLING = 'billing';
    const SHIPPING = 'shipping';
    const NEWSPAPER = 'newspaper';
}
