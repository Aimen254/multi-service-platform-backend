<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static PlatformFeeInFixedAmount()
 * @method static static PlatformFeeInPercentage()
 */
final class PlatfromFeeType extends Enum
{
    const PlatformFeeInFixedAmount = 0;
    const PlatformFeeInPercentage = 1;
}
