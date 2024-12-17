<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class CalendarEventStatus extends Enum
{
    const Going = 'going';
    const Not_Going = 'not_going';
    const Maybe = 'maybe';
}
