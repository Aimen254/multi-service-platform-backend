<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
  * @method static static Pending()
  * @method static static Accepted()
  * @method static static Ready_For_Collection()
  * @method static static Ready_For_Delivery()
  * @method static static Out_For_Delivery()
  * @method static static Delivery_Failed()
  * @method static static Completed()
  * @method static static Cancelled()
  * @method static static Returned()
  * @method static static Refunded()
  * @method static static Processing()
  * @method static static Partially_Refunded()
  * @method static static Refund_Failed()
  * @method static static Rejected()
 */
final class OrderStatus extends Enum
{
    const Pending = 1;
    const Accepted = 2;
    const Ready_For_Collection = 3;
    const Ready_For_Delivery = 4;
    const Out_For_Delivery = 5;
    const Delivery_Failed = 6;
    const Completed = 7;
    const Cancelled = 8;
    const Returned = 9;
    const Refunded = 10;
    const Processing = 11;
    const Partially_Refunded = 12;
    const Refund_Failed = 13;
    const Rejected = 14;
}
