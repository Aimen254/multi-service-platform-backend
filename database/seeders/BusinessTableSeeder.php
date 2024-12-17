<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Business;;

use App\Models\StandardTag;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Traits\BusinessStreetAddress;

class BusinessTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $businessOwner = User::whereEmail('businessOwner@interapptive.com')->first();

        $businesses = [
            // [
            //     "owner_id" => $businessOwner->id,
            //     "name" => "Calendars",
            //     "slug" => "calender",
            //     "email" => "gethelp@calendars.com",
            //     "phone" => "1-888-422-5637",
            //     "mobile" => "1-800-366-3645",
            //     "message" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
            //     "short_description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
            //     "long_description" => "Calendars.com, through its parent company Go! Retail Group, was founded in 1999 and is home to the world’s largest selection of calendars. With more than 12,000 products online, Calendars.com also offers an expansive selection of games, toys, puzzles, and gifts.

            //     The company's primary product mix is calendars, but the selection of games, toys, and puzzles has been growing each year. There are about 8,700 varieties of calendars sold on Calendars.com, from artists like Charles Wysocki, Thomas Kinkade, Mary Engelbreit, Linda Nelson Stocks and Lowell Herrero. Calendars.com has also offered licensed calendars featuring popular music artists like: Taylor Swift, Beatles, Elvis Presley, BTS, Billie Eilish, Ed Sheeran, RUSH, John Lennon and many more.

            //     Calendars.com sells a large selection of games, toys, puzzles and gifts. The company's largest franchise has over 65 licensed versions from The Walking Dead, Big Bang Theory, The Hobbit, and even The Legend of Zelda. If board games aren't your thing, there's a selection of over 500 puzzles online with more being added daily.

            //     Through its international partners, Calendars.com has extended its selection, and world class service, to online customers in Canada, the UK and Australia / New Zealand on the websites Calendarclub.ca, Calendarclub.co.uk and Calendars.com.au.

            //     Go! Retail Group was co-founded by CEO Marc Winkelman in 1993 and is the largest operator of seasonal, mall-based pop-up stores in the world. Go! Retail Group is the parent company to several strong retail brands, including Go!, Calendars.com and Attic Salt. Go! Retail Group has a vibrant and unique culture, with a focus on fun and hard work. In addition to its growing ecommerce operations, the company operates over 500 seasonal stores and up to 100 year round stores in shopping malls, outlets, and lifestyle centers in the US, Canada, England, Ireland, Australia, and New Zealand. The Go! Retail Group brands have been featured on Good Morning America, Huffington Post, BuzzFeed, and many more.",
            //     "shipping_and_return_policy" => "We offer a 30-day, money-back guarantee. If you are not completely satisfied with your purchase, you may return it to us within 30 days for a prompt refund, or you may exchange the item for an item of equal or lesser value.

            //     All items must be in their original condition. Please return items in their original shrink wrap.
            //     We are willing to make exceptions as needed, as long as the item has not been written in or shows no signs of usage such as creases, folds, etc.
            //     All items should be returned with adequate packing materials to ensure that they are not creased or otherwise damaged in the return shipment.
            //     You will receive a full refund of the product's purchase price minus original shipping charges.
            //     Our goal is to process all returns within 5 business days of receiving them back in our warehouse. In December and January, please allow up to 7-10 business days due to additional, holiday volume.
            //     Calendars.com is not responsible for lost returns.
            //     Customers are responsible for the cost and arrangement of return shipping.
            //     Order Contained Damaged or Incorrect Items:
            //     Contact our Customer Service Team at 800-366-3645 or via email and we will process your return for you!

            //     All Other Returns or Exchanges:
            //     Send us your return, with a note listing order ID, by your preferred shipping method to:
            //     Calendars.com
            //     ATTN: Returns Processing
            //     6411 Burleson Road
            //     Austin, TX 78744-1414",
            //     "address" => "Calendars 6411 Burleson Road Austin, TX 78744",
            //     "latitude" => "30.203781717432573",
            //     "longitude" => "-97.71572805882856"
            // ],
            [
                "owner_id" => $businessOwner->id,
                "name" => "Frock Candy",
                "slug" => "frock-candy",
                "email" => "orders@frockcandy.com",
                "phone" => "+1 888-391-1165",
                "mobile" => "225 216 0216",
                "message" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "short_description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "long_description" => "Frock Candy is a local women's fashion retailer founded in Baton Rouge, and we were named the 2019 Best of 225 winner for Best Local Women's Boutique.
                Our online Orders Department is separate from in store locations, and therefore cannot hold any items. If you'd like an item held for you in store, please call your preferred location, and a sales associate will be happy to check for your size. If your size is available, they can hold your item(s) until the end of the day.",
                "shipping_and_return_policy" => "Online returns are accepted on unwashed, unworn merchandise in original condition/tags attached within 21 days of the original invoice date for a full refund.  Returned packages must be postmarked within 14 days of the invoice date in order for us to receive within the 21-day timeframe.
                Shipped Orders placed online may be returned/exchanged in-store following the In-store Return Policy listed below.
                In-store Pickup orders placed online, will follow the In-store Return Policy(see below).
                If you receive an item that is defective or damaged, email us at orders@frockcandy.com with the damaged information so that we may issue a return label for the exchange.
                The following items will not be accepted for return/exchange due to hygienic reasons: tights, socks, intimates, and bodysuits.
                Giftcards are non-refundable.
                Shoes must be returned in their original, undamaged shoebox to receive a full refund. If a shoe is returned using the shoebox as the shipping box, we will only issue a refund of 50% in order to discount that item and resell.
                Shoes will not be accepted for return without their original, undamaged shoebox. Shoes received without their shoebox will be returned to the customer at their expense.
                Once your return is received and inspected, we will send you an email notifying you that we have received your returned item, along with the approved refund amount to the original form of payment.
                Please see the 'Online Shipping Information' section below to see the detailed return shipping process.",
                "address" => "Frok Candy 7474 Corporate Blvd, #305 Baton Rouge, LA 70809",
                "latitude" => "30.430522764689638",
                "longitude" => "-91.11368160300262"
            ],
            [
                "owner_id" => $businessOwner->id,
                "name" => "Carriages Fine Clothier",
                "slug" => "carriages-fine-clothier",
                "email" => "will@carriagesbr.com",
                "phone" => "225-926-6892",
                "mobile" => "",
                "message" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "short_description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "long_description" => "We do our best to ensure that all product seen online has available inventory.  However, sometimes an item could be sold out shortly before or after your purchase.  If this happens, we will notify you by email when your product will be available to ship.  This is why an accurate email and phone number is crucial when placing your order.",
                "shipping_and_return_policy" => "If you are not 100% satisfied with your purchase, you may exchange the item or return it for a full refund. Items should be returned new, unworn and with all original tags and labels still attached.  In addition, shoes should be returned unworn in their original, undamaged shoe box.  We reserve the right to refuse returns which are not in the above specified conditions.  Returns and exchanges will only be accepted within 30 days of the purchase date. If a full price item is put on sale after date of purchase, the new sale price will be honored for the refund.  

                If you would like to return or exchange an item, please send an email to joey@carriagesbr.com expressing your concern. Once we receive your request, we'll send you a pre-paid return shipping label which you can use to send your merchandise to us.
                
                Please mail returns/exchanges to:
                
                Carriages Fine Clothier                      
                
                Attention: Return
                
                7606 Old Hammond Hwy
                
                Baton Rouge, LA 70809
                
                Please include a copy of your receipt with your return.  If you do not have your receipt, include your name, order number and the best phone number to be contacted by.  Once your return is received and processed, we'll refund you in the original form of payment.  Please allow 3 - 5 business days for your refund to be reflected on your account.
                
                Note: All sale items are final and non-refundable.
                
                *Note: All Holiday returns must be made by January 15th!!
                
                Please feel free to contact us anytime:
                
                By email: joey@carriagesbr.com      
                
                By phone: (225) 926-6892",
                "address" => "Carriage Fine Clothier BATON ROUGE, LA, 70809 7620 OLD HAMMOND HWY",
                "latitude" => "30.431028405223667",
                "longitude" => "-91.10766721649459"
            ],
            // [
            //     "owner_id" => $businessOwner->id,
            //     "name" => "Currie",
            //     "slug" => "currie",
            //     "email" => "cbohn@shopcurrie.com",
            //     "phone" => "225-928-1185",
            //     "mobile" => "",
            //     "message" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
            //     "short_description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
            //     "long_description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged",
            //     "shipping_and_return_policy" => "If merchandise is returned within 7 day of purchase the customer will receive STORE CREDIT ONLY. No returns without original sales receipt. Original sales tags must be attached for return.
            //     NO CASH OR CREDIT CARD REFUNDS.
            //     No returns or exchanges on accessories, shoes, jewelry, handbags, lingerie, special orders, or sale merchandise.
            //     ALL sale items are FINAL SALE.",
            //     "address" => "Currie 7575 Jefferson Hwy, Suite A, Baton Rouge, LA 70806",
            //     "latitude" => "30.432008302621014",
            //     "longitude" => "-91.10921106067453"
            // ],
            [
                "owner_id" => $businessOwner->id,
                "name" => "Messengers Gifts",
                "slug" => "messengers-gifts",
                "email" => "messengersgifts@gmail.com",
                "phone" => "(225) 250-5128",
                "mobile" => "",
                "message" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "short_description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "long_description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged",
                "shipping_and_return_policy" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged",
                "address" => "Messengers Gifts, George Oneal Road, Baton Rouge, LA, USA",
                "latitude" => "30.406699610349378",
                "longitude" => "-91.01561606067526"
            ],
            [
                "owner_id" => $businessOwner->id,
                "name" => "HighlandSide",
                "slug" => "highlandside",
                "email" => "highlandside@hotmail.com",
                "phone" => "225 754 7400",
                "mobile" => "",
                "message" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "short_description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "long_description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged",
                "shipping_and_return_policy" => "All sales final.  No Refunds.  Store Credit or Exchange within 10 days of purchase with receipt only.  All sale merchandise is final sale, no store credit or exchange will be given for any sale merchandise.

                Returns
                Our policy lasts 10 days. If 10 days have gone by since your purchase, unfortunately we can’t offer you a store credit or exchange.
                
                To be eligible for a return, your item must be unused and in the same condition that you received it. It must also be in the original packaging.
                
                Non-returnable items:
                Gift cards
                Monogrammable Items
                Custom Ordered Items
                Preordered Items
                
                To complete your return, we require a receipt or proof of purchase.
                
                Any item not in its original condition, damaged or missing parts for reasons not due to our error or Any item that is returned more than 10 days after delivery are not eligible for a store credit or exchange.",
                "address" => "17732 Highland Rd Baton Rouge, LA 70810",
                "latitude" => "30.344352439844457",
                "longitude" => "-91.03174516067683"
            ],
            [
                "owner_id" => $businessOwner->id,
                "name" => "H Kyle Boutique",
                "slug" => "h-kyle-boutique",
                "email" => "hello@shophkyle.com",
                "phone" => "+1 225-744-7902",
                "mobile" => "",
                "message" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "short_description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "long_description" => "H Kyle was started in the fall of 2013 by Tia in memory of her late mother, Martha. H Kyle has always been a dream of hers and she hopes to pass along her love of fashion to her own daughter, Harper Kyle. Located on the outskirts of Baton Rouge, LA, H Kyle is a place where you can find cute gifts and accessories, as well as contemporary and fashionable ladies' apparel in the parish of Ascension.",
                "shipping_and_return_policy" => "1. What is your return policy?

                All sales final. No refunds. All exchanges must be made within 7 days of the ORIGINAL purchase date for in-store purchases. Items must be unworn with tags attached and must be in sellable condition. The following items are final sale and cannot be returned for store credit or exchanged for other items: all jewelry items, custom apparel, Corkcicles, sale or promotional (discounted) items, accessories (Haute Shore), undergarments (bralettes), and holiday/seasonal items.

                2. What is the return policy for online orders?

                Shipping charges are non-refundable. Returns are eligible for STORE CREDIT or EXCHANGE ONLY. Please contact us at hello@shophkyle.com with any issues and return package within 3 days of receiving. The note care must be in the package for your credit to be processed. After we have accessed the clothing, we will process your return in the form of a store credit",
                "address" => "H Kyle Boutique 14601 Airline Hwy #106, Gonzales, LA 70737, United States",
                "latitude" => "30.28204188027182",
                "longitude" => "-90.95372143639504"
            ],
            [
                "owner_id" => $businessOwner->id,
                "name" => "Gamers Paradise LA",
                "slug" => Str::slug("Gamers Paradise LA"),
                "email" => "hello@shophkyle.com",
                "phone" => "+1 225-744-7902",
                "mobile" => "",
                "message" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "short_description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "long_description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "shipping_and_return_policy" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "address" => "H Kyle Boutique 14601 Airline Hwy #106, Gonzales, LA 70737, United States",
                "latitude" => "30.28204188027182",
                "longitude" => "-90.95372143639504"
            ],
            [
                "owner_id" => $businessOwner->id,
                "name" => "Sweet Baton Rouge",
                "slug" => Str::slug('Sweet Baton Rouge'),
                "email" => "hello@shophkyle.com",
                "phone" => "+1 225-744-7902",
                "mobile" => "",
                "message" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "short_description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "long_description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "shipping_and_return_policy" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "address" => "H Kyle Boutique 14601 Airline Hwy #106, Gonzales, LA 70737, United States",
                "latitude" => "30.28204188027182",
                "longitude" => "-90.95372143639504"
            ],
            [
                "owner_id" => $businessOwner->id,
                "name" => "Perlis",
                "slug" => Str::slug('Perlis'),
                "email" => "hello@shophkyle.com",
                "phone" => "+1 225-744-7902",
                "mobile" => "",
                "message" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "short_description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "long_description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "shipping_and_return_policy" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "address" => "H Kyle Boutique 14601 Airline Hwy #106, Gonzales, LA 70737, United States",
                "latitude" => "30.28204188027182",
                "longitude" => "-90.95372143639504"
            ],
            [
                "owner_id" => $businessOwner->id,
                "name" => "Giggles",
                "slug" => Str::slug('Giggles'),
                "email" => "hello@shophkyle.com",
                "phone" => "+1 225-744-7902",
                "mobile" => "",
                "message" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "short_description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "long_description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "shipping_and_return_policy" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "address" => "H Kyle Boutique 14601 Airline Hwy #106, Gonzales, LA 70737, United States",
                "latitude" => "30.28204188027182",
                "longitude" => "-90.95372143639504"
            ],
            [
                "owner_id" => $businessOwner->id,
                "name" => "Little Wars",
                "slug" => Str::slug('Little Wars'),
                "email" => "hello@shophkyle.com",
                "phone" => "+1 225-744-7902",
                "mobile" => "",
                "message" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "short_description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "long_description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "shipping_and_return_policy" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "address" => "H Kyle Boutique 14601 Airline Hwy #106, Gonzales, LA 70737, United States",
                "latitude" => "30.28204188027182",
                "longitude" => "-90.95372143639504"
            ],
            [
                "owner_id" => $businessOwner->id,
                "name" => "Victoria's Toys Station",
                "slug" => Str::slug("Victoria's Toys Station"),
                "email" => "hello@shophkyle.com",
                "phone" => "+1 225-744-7902",
                "mobile" => "",
                "message" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "short_description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "long_description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "shipping_and_return_policy" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                "address" => "H Kyle Boutique 14601 Airline Hwy #106, Gonzales, LA 70737, United States",
                "latitude" => "30.28204188027182",
                "longitude" => "-90.95372143639504"
            ]
        ];

        // global tags
        $retailTag = StandardTag::whereSlug('retail')->first();
        $messengerGiftsTagSlug = [
            'home-garden',
        ];
        $frockCandyTagSlug = [
            'fashion',
        ];
        $messengerGiftsTag = StandardTag::whereIn('slug', $messengerGiftsTagSlug)->get()->pluck('id')->toArray();
        $frockCandyTag = StandardTag::whereIn('slug', $frockCandyTagSlug)->get()->pluck('id')->toArray();
        $electronics = StandardTag::where('slug', 'electronics')->get()->pluck('id')->toArray();

        foreach ($businesses as $business) {
            $business = Business::create($business);
            // BusinessStreetAddress::streetAddress($business);
            $this->businessSchedule($business);
            switch ($business->slug) {
                case 'messengers-gifts':
                    $tagData = Arr::collapse([[$retailTag->id], $messengerGiftsTag]);
                    break;

                case 'h-kyle-boutique':
                    $tagData = Arr::collapse([[$retailTag->id], $frockCandyTag]);
                    break;
                case 'currie':
                case 'frock-candy':
                    $tagData = Arr::collapse([[$retailTag->id], $frockCandyTag]);
                    break;

                case 'highlandside':
                    $tagData = [$retailTag->id];
                    break;

                case 'carriages-fine-clothier':
                    $tagData = Arr::collapse([[$retailTag->id], $frockCandyTag]);
                    break;

                case 'gamers-paradise-la':
                case 'gameware':
                    $tagData = Arr::collapse([[$retailTag->id], $electronics]);

                default:
                    $tagData = [$retailTag->id];
                    break;
            }
            $business->standardTags()->sync($tagData);
        }
    }

    private function businessSchedule($business)
    {
        switch ($business->slug) {
            case 'frock-candy':
                $daysAndTiming = [
                    'sunday' => [
                        'status' => 'inactive'
                    ],
                    'monday' => [
                        'status' => 'active',
                        'timing' => [
                            'open_at' => '10:00:00',
                            'close_at' => '19:00:00'
                        ]
                    ],
                    'tuesday' => [
                        'status' => 'active',
                        'timing' => [
                            'open_at' => '10:00:00',
                            'close_at' => '19:00:00'
                        ]
                    ],
                    'wednesday' => [
                        'status' => 'active',
                        'timing' => [
                            'open_at' => '10:00:00',
                            'close_at' => '19:00:00'
                        ]
                    ],
                    'thursday' => [
                        'status' => 'active',
                        'timing' => [
                            'open_at' => '10:00:00',
                            'close_at' => '19:00:00'
                        ]
                    ],
                    'friday' => [
                        'status' => 'active',
                        'timing' => [
                            'open_at' => '10:00:00',
                            'close_at' => '19:00:00'
                        ]
                    ],
                    'saturaday' => [
                        'status' => 'active',
                        'timing' => [
                            'open_at' => '10:00:00',
                            'close_at' => '18:00:00'
                        ]
                    ]
                ];
                break;

            case 'carriages-fine-clothier':
                $daysAndTiming = [
                    'sunday' => [
                        'status' => 'active',
                        'timing' => [
                            'open_at' => '12:00:00',
                            'close_at' => '15:00:00'
                        ]
                    ],
                    'monday' => [
                        'status' => 'active',
                        'timing' => [
                            'open_at' => '10:00:00',
                            'close_at' => '18:00:00'
                        ]
                    ],
                    'tuesday' => [
                        'status' => 'active',
                        'timing' => [
                            'open_at' => '10:00:00',
                            'close_at' => '18:00:00'
                        ]
                    ],
                    'wednesday' => [
                        'status' => 'active',
                        'timing' => [
                            'open_at' => '10:00:00',
                            'close_at' => '18:00:00'
                        ]
                    ],
                    'thursday' => [
                        'status' => 'active',
                        'timing' => [
                            'open_at' => '10:00:00',
                            'close_at' => '18:00:00'
                        ]
                    ],
                    'friday' => [
                        'status' => 'active',
                        'timing' => [
                            'open_at' => '10:00:00',
                            'close_at' => '18:00:00'
                        ]
                    ],
                    'saturaday' => [
                        'status' => 'active',
                        'timing' => [
                            'open_at' => '10:00:00',
                            'close_at' => '18:00:00'
                        ]
                    ]
                ];
                break;

            case 'h-kyle-boutique':
                $daysAndTiming = [
                    'sunday' => [
                        'status' => 'inactive'
                    ],
                    'monday' => [
                        'status' => 'inactive'
                    ],
                    'tuesday' => [
                        'status' => 'active',
                        'timing' => [
                            'open_at' => '12:00:00',
                            'close_at' => '18:00:00'
                        ]
                    ],
                    'wednesday' => [
                        'status' => 'active',
                        'timing' => [
                            'open_at' => '12:00:00',
                            'close_at' => '18:00:00'
                        ]
                    ],
                    'thursday' => [
                        'status' => 'active',
                        'timing' => [
                            'open_at' => '12:00:00',
                            'close_at' => '18:00:00'
                        ]
                    ],
                    'friday' => [
                        'status' => 'active',
                        'timing' => [
                            'open_at' => '12:00:00',
                            'close_at' => '18:00:00'
                        ]
                    ],
                    'saturaday' => [
                        'status' => 'active',
                        'timing' => [
                            'open_at' => '11:00:00',
                            'close_at' => '14:00:00'
                        ]
                    ]
                ];
                break;
            default:
                $daysAndTiming = [];
                break;
        }

        foreach ($daysAndTiming as $key => $dayAndTime) {
            $schedule = $business->businessschedules()->where('name', $key)->first();
            if ($dayAndTime['status'] == 'active') {
                $schedule->scheduletimes()->create($dayAndTime['timing']);
                $schedule->update(['status' => $dayAndTime['status']]);
            }
        }
    }
}
