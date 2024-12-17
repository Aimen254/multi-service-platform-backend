<?php

namespace App\Enums\Business;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class BusinessStatus extends Enum
{
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';
    const APPROVED = 'approved';
    const REJECTED = 'rejected';
    const WAITING_APPROVAL = 'waiting for approval';
}
