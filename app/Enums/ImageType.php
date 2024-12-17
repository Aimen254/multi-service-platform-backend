<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class ImageType extends Enum
{
    const Thumbnail =   'thumbnail';
    const Logo =   'logo';
    const Banner = 'banner';
    const Image =   'image';
    const Video =   'video';
    const Author = 'author';
    const SecondaryBanner = 'secondaryBanner';
    const Resume = 'resume';
}

