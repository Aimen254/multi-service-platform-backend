<?php

namespace App\Transformers;

use Illuminate\Support\Carbon;
use stdClass;
use App\Models\Setting;
use App\Models\Wishlist;
use App\Models\StandardTag;
use Sabberworm\CSS\Settings;
use App\Transformers\Transformer;
use Illuminate\Support\Facades\DB;
use App\Transformers\AddressTransformer;
use App\Transformers\CreditCardTransformer;
use App\Transformers\UserPaymentTransformer;

class UserTransformer extends Transformer
{

    public function transform($user, $options = null)
    {
        // get public profile
        $publicProfile = $this->getPublicProfile($user, $options);
        $userResponse = [
            'id' => (int)$user->id ?? NULL,
            'first_name' => (string)$user->first_name,
            'last_name' => (string)$user->last_name,
            'name' => (string) $publicProfile && $publicProfile->is_name_visible && $publicProfile->name ? $publicProfile->name : $user->first_name . ' ' . $user->last_name,
            'email' => (string)$user->email,
            'phone' => (string)$user->phone,
            'avatar' => (string) getImage($publicProfile?->image ?? $user->avatar, 'avatar', $user->is_external),
            'dob' => (string) $user->dob,
            'about' => (string) $publicProfile && $publicProfile?->description ? $publicProfile?->description : $user->about,
            'in_wishlist' => $this->usersExistsInWishlist($user?->id),
            'email_verified_at' => (string) $user->email_verified_at,
            'stripe_onboarding' => (bool) $user->completed_stripe_onboarding,
            'bank_connected' => $user->stripe_bank_id ? true : false,
            'products_count' => (int) $user->products_count,
            'user_type' => (string) $user->user_type,
            'neighborhood_name' => (string) $user->neighborhood_name,
            'cover_img' => (string) getImage($publicProfile?->cover_image ?? $user->cover_img, 'banners', $user->is_external),
            'weekly_published' => $user->weekly_published,
            'reviews_avg' => (float) $user->reviews()->avg('rating'),
            'status' => (string) $user->status,
        ];
        if ($user->user_type == 'newspaper') {
            $newsPapaperLogo = Setting::where('key', 'newspaper_logo')->first();
            $userResponse['news_paper_logo'] =  (string) getImage($newsPapaperLogo->value, 'image');
        }

        if (isset($options['access_token'])) {
            $userResponse['access_token'] = $options['access_token'];
            $userResponse['token_expiry_time'] = config('sanctum.expiration');
            $currentDateTime = Carbon::now();
            $expirationMinutes = config('sanctum.expiration');
            $userResponse['expiry_time'] = $currentDateTime->addMinutes($expirationMinutes);
        }

        if (isset($options['withAddress']) && $options['withAddress']) {
            $address = $user->addresses()
                ->where('status', 'active')
                ->orderByDesc('is_default') // Orders by is_default in descending order (1 first, then 0)
                ->first();
            $userResponse['location'] = $address ? (new AddressTransformer)->transform($address) : new stdClass;
        }

        if (isset($options['withDetails']) && $options['withDetails']) {
            $address = $user->addresses()->orderBy('id', 'desc')->get();
            $userResponse['addresses'] = (new AddressTransformer)->transformCollection($address);
            $userResponse['payment_settings'] = (new CreditCardTransformer)->transformCollection($user->cards);
        }

        if (isset($options['withProducts'])) {
            $userResponse['products'] = (new ProductTransformer)->transformCollection($user?->products->take(4));
        }

        if ((isset($options['levelOneTag']) && isset($options['withBusiness'])) && ($options['levelOneTag'] && $options['withBusiness'])) {
            $levelOne = StandardTag::where('id', $options['levelOneTag'])
                ->orWhere('slug', $options['levelOneTag'])
                ->where('type', 'module')->firstOrFail();
            $busienssOptions['module'] = $levelOne?->slug;
            $userResponse['business'] = $user?->business ? (new BusinessTransformer)->transform($user?->business, $busienssOptions) : new stdClass;
        }

        if (isset($options['getLevelTags'])) {
            $userResponse['level_one_tag'] = $this->getLevelTags($user);
            $userResponse['level_two_tag'] = $this->getLevelTags($user, 'level_two_tag');
            $userResponse['level_three_tag'] = $this->getLevelTags($user, 'level_three_tag');
            $userResponse['level_four_tag'] = $this->getLevelTags($user, 'level-four-count');
        }

        return $userResponse;
    }

    private function usersExistsInWishlist($id)
    {
        $user = auth('sanctum')->user();
        $tag =  request()->input('module') ? request()->input('module') : request()->input('module_id');
        $module = StandardTag::where('id', $tag)->orWhere('slug', $tag)->first();
        if ($user) {
            $wishlist = Wishlist::where('user_id', $user->id)
                ->where('module_id', $module?->id)
                ->where('model_id', $id)
                ->where('model_type', 'App\Models\User')->first();
            return $wishlist ? true : false;
        } else {
            return false;
        }
    }

    // get level <one, two, three> tags
    private function getLevelTags($user, ?string $tagType = null)
    {
        $levelOneTag = $user->standardTags()->whereHas('levelOne')->first();

        $levelTwoTag = $levelThreeTag = $levelFourTags = null;

        if (isset($levelOneTag)) {
            $levelTwoTag = $user->standardTags()->whereHas('levelTwo', function ($query) use ($levelOneTag) {
                $query->where('L1', $levelOneTag?->id);
            })->first();
        }

        if (isset($levelOneTag) && isset($levelTwoTag)) {
            $levelThreeTag = $user->standardTags()->whereHas('levelThree', function ($query) use ($levelOneTag, $levelTwoTag) {
                $query->where('L1', $levelOneTag->id)->where('L2', $levelTwoTag->id);
            })->first();
        }

        // if (isset($levelOneTag) && isset($levelTwoTag) && isset($levelThreeTag)) {
        //     $levelFourTags = StandardTag::whereHas('productTags', function ($query) use ($levelOneTag, $levelTwoTag, $levelThreeTag) {
        //         $query->when(in_array($levelOneTag->slug, ['retail', 'automotive', 'boats', 'employment', 'services', 'government']), function ($innerQuery) {
        //             $innerQuery->active();
        //         }, function ($innerQuery) {
        //             $innerQuery->where('status', 'active');
        //         });
        //         $query->whereHas('standardTags', function ($subQuery) use ($levelOneTag, $levelTwoTag, $levelThreeTag) {
        //             $subQuery->whereIn('id', [$levelOneTag->id, $levelTwoTag->id, $levelThreeTag->id])
        //                 ->select('*', DB::raw('count(*) as total'))
        //                 ->having('total', '>=', 3);
        //         });
        //     })->whereHas('tagHierarchies', function ($query) use ($levelOneTag, $levelTwoTag, $levelThreeTag) {
        //         $query->where('level_type', 4)->where('L1', $levelOneTag->id)->where('L2', $levelTwoTag->id)->where('L3', $levelThreeTag->id);
        //     })->get();
        // }




        switch ($tagType) {
            case 'level_two_tag':
                return $levelTwoTag;
            case 'level_three_tag':
                return $levelThreeTag;
                // case 'level-four-count':
                //     return $levelFourTags?->count() === 1 ? $levelFourTags[0] : null;
            default:
                return $levelOneTag;
        }
    }

    private function getPublicProfile($user, $options)
    {
        $profile = null;
        if (isset($options['levelOneTag']) && $options['levelOneTag'] && $user) {
            $profile = $user->publicProfiles()->where('module_id', $options['levelOneTag'])->first();
        }
        return $profile;
    }
}
