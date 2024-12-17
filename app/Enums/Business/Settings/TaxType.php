<?php

namespace App\Enums\Business\Settings;

use BenSampo\Enum\Enum;

/**
 * @method static TaxIncludedOnPrice()
 * @method static TaxNotIncludedOnPrice()
 */
final class TaxType extends Enum
{
    const TaxIncludedOnPrice = 0;
    const TaxNotIncludedOnPrice = 1;
    const TaxNotApplicable = 2;
}
