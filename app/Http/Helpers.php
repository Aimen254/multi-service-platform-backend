<?php

use Carbon\Carbon;
use App\Models\Setting;
use App\Models\Business;;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

/**
 * Set flash messages
 */
function flash($message, $style = 'info', $link = null)
{
    session()->flash('flash.banner', $message);
    session()->flash('flash.bannerStyle', $style);
    session()->flash('flash.link', $link);
}

/**
 * Get logged in user role
 * @return object
 */
function getRole($user)
{
    return $user && $user->roles ? $user->roles[0] : NULL;
}

/**
 * Get role permissions
 * @return object
 */
function getPermissionsName($role)
{
    return $role->permissions()->pluck('name');
}

/**
 * @param $file
 * @param $directory
 * @param $width
 * @return string
 * save resize image in storage
 */
function saveResizeImage($file, $directory, $width, $height = null, $type = 'jpg')
{
    if (!Storage::exists($directory)) {
        Storage::makeDirectory("$directory");
    }
    $is_preview = strpos($directory, 'previews') !== false;
    $filename = Str::random() . time() . '.' . $type;
    $path = "$directory/$filename";
    $img = \Image::make($file)->orientate()->encode($type, 80)->resize($width, $height, function ($constraint) use ($height) {
        if (!$height) {
            $constraint->aspectRatio();
        }
        // $constraint->upsize();
    });
    $resource = $img->stream()->detach();
    Storage::disk('public')->put($path, $resource, 'public');
    return $path;
}

function isDataDirty($originalData, $requestData)
{
    return !empty(array_diff_assoc($originalData->toArray(), $requestData));
}

function uploadFirebaseConfigFile($type)
{
    if (!is_string($type['value'])) {
        $file_path = base_path('firebase_config_file.json');
        file_put_contents($file_path, $type['value']->getContent());
        return 'firebase_config_file.json';
    }
    return $type['value'];
}

function uploadPdfFile($file, $directory, $type = 'pdf')
{
    if (!Storage::exists($directory)) {
        Storage::makeDirectory($directory);
    }

    // Generate a unique filename for the PDF file
    $filename = Str::random() . time() . '.' . $type;

    // Store the PDF file content using the put method
    $file->storeAs($directory, $filename, 'public');

    // Return the path to the saved file
    return "$directory/$filename";
}


/**
 * @param $file
 * delete a file
 */
function deleteFile($path)
{
    if (!empty($path) && file_exists('app/' . $path)) {
        unlink(storage_path('app/' . $path));
    }

    $storage_path = 'storage/' . $path;
    $public_path = public_path($storage_path);
    if (!empty($path) && file_exists($public_path)) {
        unlink($public_path);
    }
}

/**
 * @param $Time
 * convert a time
 */
function convertTime($time, $format)
{
    if ($time !== null) {
        return Carbon::parse($time)->format($format);
    }
}

/**
 * @param $Time
 * convert date
 */
function convertDate($date, $format)
{

    if ($date !== null) {
        return Carbon::parse($date)->format($format);
    }
}

function CommaSeparateDateValues($dateValues)
{
    $multipleDateArray = array();
    if ($dateValues !== null) {
        foreach ($dateValues as $date) {
            $multipleDateArray[] = $date['date'];
        }
    }
    return implode(',', $multipleDateArray);
}

function ArrayDateValues($dateValues)
{
    $DateArray = array();
    if ($dateValues !== null) {
        $dateJsonDecode = explode(',', $dateValues);
        foreach ($dateJsonDecode as $key => $date) {
            $DateArray[$key] = array('date' => $date);
        }
    }
    return $DateArray;
}

/**
 * @param $uuid
 */
function getBusinessDetails($uuid, $module = null)
{
    return Business::whereUuid($uuid)->first();
}

function setDateValues($value)
{
    if ($value && request()->timeZone) {
        date_default_timezone_set(request()->timeZone);
        return date("Y-m-d H:i:s", strtotime($value));
    }
}

function apiPagination($collection, $limit = null)
{
    $params = http_build_query(request()->except('page'));
    $total_pages = $limit ? (int) ceil($collection?->total() / $limit) : null;
    $next = $collection->nextPageUrl();
    $previous = $collection->previousPageUrl();

    if ($params) {
        if ($next) {
            $next .= "&{$params}";
        }
        if ($previous) {
            $previous .= "&{$params}";
        }
    }
    $meta = [
        "next" => (string)$next,
        "previous" => (string)$previous,
        "per_page" => (int)$collection->perPage(),
        "total" => (int)$collection?->total(),
        "current_page" => (int)$collection->currentPage(),
        'total_pages' => $total_pages,
        'first' => (int)$collection->firstItem(),
        'last' => (int)$collection->lastItem()
    ];
    return $meta;
}

/**
 * Get Business Owner Name From Business
 */
function getOwnerName($businessOwner)
{
    return $businessOwner->first_name . ' ' . $businessOwner->last_name;
}

/**
 * Get Image Url From image.
 */
function getImageUrl($image, $path = false)
{
    //if you are sending image object then do not send second parameter and if you are sending direct path then send second parameter as true.
    if (!$path) {
        return $image && $image->path ? env('FILE_URL') . '/' . $image->path : null;
    } else {
        return $image ? env('FILE_URL') . '/' . $image : null;
    }
}

/**
 * Get image url.
 */
function getImage($image, $type, $isExternal = false)
{
    $imageUrl = '';
    if (empty($image)) {
        switch ($type) {
            case 'avatar':
                $placeHolder = asset('images/no_avatar.jpg');
                break;
            case 'logo':
            case 'icon':
            case 'image':
                $placeHolder = asset('images/placeholder.png');
                break;
            case 'banners':
                $placeHolder = asset('images/cover.jpg');
                break;
        }
    } else {
        $imageUrl = $isExternal ? $image : url(Storage::url($image));
    }

    return isset($placeHolder) ? $placeHolder : $imageUrl;
}

/**
 * Set number formats.
 */
function numberFormat($number)
{
    $decimalLength = 1;
    $decimalSeparator = ',';
    $formatSettings = Setting::where('group', 'number_format_settings')->get();
    foreach ($formatSettings as $key => $decimalSetting) {
        if ($decimalSetting->key == 'decimal_length') {
            $decimalLength = $decimalSetting->value;
        }
        if ($decimalSetting->key == 'decimal_separator') {
            $decimalSeparator = $decimalSetting->value;
        }
    }
    return str_replace(
        '.',
        $decimalSeparator,
        number_format((float)$number, $decimalLength, '.', ',')
    );
}

function timeFormat($time, $withDate = true)
{
    $formatSettings = Setting::select("value")->where('group', 'time_format_settings')->first();
    if ($formatSettings->value == '12 hours') {
        return $withDate ? Carbon::parse($time)->format('d M Y h:i A')
            : Carbon::parse($time)->format('h:i A');
    }
    return $withDate ? Carbon::parse($time)->format('d M Y H:i')
        : Carbon::parse($time)->format('H:i');
}
function checkImageIsBroken($image)
{
    return true;
    if ($image->is_external) {
        $ch = curl_init($image->path);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $code == 200 ? true : false;
    }
    return Illuminate\Support\Facades\File::exists(getImage($image->path, 'image', true));
}
/**
 * Check the delivery area is inside the circle radius.
 */
function vincentyGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
{
    $latFrom = deg2rad($latitudeFrom);
    $lonFrom = deg2rad($longitudeFrom);
    $latTo = deg2rad($latitudeTo);
    $lonTo = deg2rad($longitudeTo);

    $lonDelta = $lonTo - $lonFrom;
    $a = pow(cos($latTo) * sin($lonDelta), 2) +
        pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
    $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

    $angle = atan2(sqrt($a), $b);
    return $angle * $earthRadius;
}

/**
 * Check coupon is applicable
 * @param $cart, $coupon, $updateModel
 * @return array
 */
function isCouponApplicable($cart, $coupon, $updateModel = false)
{
    $response = [
        'status' => true,
        'message' => 'Coupon verified'
    ];
    switch ($coupon->coupon_type) {
        case 'business':
            if ($coupon->discount_type == 'fixed') {
                $cartTotal = $cart->items()->where('business_id', request()->business_id)->sum('actual_price');
                if ($cartTotal < $coupon->discount_value) {
                    $response = [
                        'status' => false,
                        'message' => 'Coupon not applicable, total amount is less than discount i.e $' . $coupon->discount_value
                    ];
                    if ($updateModel) {
                        $cart->update([
                            'coupon_id' => NULL,
                            'total' => calculateTotalPrice($cart),
                            'discount_price' => 0
                        ]);
                    }
                    break;
                }
            }
            break;
        case 'product':
            $response = verifyProductCoupon($cart, $coupon, $coupon->coupon_type, $response);
            break;
    }
    return $response;
}

function calculateTotalPrice($model, $cartTax = 0, $discount = 0, $applyDiscount = false)
{
    return ($applyDiscount ? ($model - $discount) : $model
    ) + $cartTax;
}

function removeCoupon($cart)
{
    $cart->update(['coupon_id' => NULL]);
}

function resetStringFormat($string)
{
    if (strtoupper($string) === $string) {
        return ucwords(strtolower($string));
    } else {
        return $string;
    }
}


function verifyProductCoupon($cart, $coupon, $type, $response)
{
    $items = $cart->items()->where(function ($query) use ($type, $coupon) {
        $query->whereHas('product.coupons', function ($subQuery) use ($coupon) {
            $subQuery->where('coupon_id', $coupon->id);
        });
    })->get();
    // if no item left in the cart on which coupon is applicable then remove the coupon from the cart
    if ($items->count() <= 0) {
        removeCoupon($cart);
        $response = [
            'status' => false,
            'message' => 'Coupon is not applicable on this cart'
        ];
    } else {
        if ($coupon->discount_type == 'fixed') {
            $couponApplicable = false;
            if (request()->path() == 'api/coupon-verification') {
                foreach ($items as $key => $item) {
                    if ($item->actual_price >= $coupon->discount_value) {
                        $couponApplicable = true;
                    }
                }

                $response = $couponApplicable
                    ? $response
                    : $response = [
                        'status' => false,
                        'message' => 'Coupon is not applicable on this cart'
                    ];
            }
        }
    }
    return $response;
}

function matchAgainstUnionConvert($keyword)
{
    return \implode(' ', array_map(function ($record) {
        return "+" . $record;
    }, \explode(' ', $keyword)));
}

function removeSpecialCharacters($string)
{
    return preg_replace('/(?<!\w)[\/\,+*&%$;:.|^!\\\\](?!\w)/', '', $string);
}

function orphanTagSlug($string)
{
    $processed_string = preg_replace('/[^\w\/,+*&%$;:.|^!\\\\]+/', '-', $string);
    $processed_string = strtolower($processed_string);
    return $processed_string;
}

function orderStatus($string)
{
    return $string ? str_replace('_', ' ', $string) : '';
}

function getFileUrl($file)
{
    return $file ? env('FILE_URL') . '/' . $file : null;
}

function formatString($string)
{
    return $string ? str_replace('-', ' ', $string) : '';
}

function getPriceFormat($price, $levelOneTag)
{
    if (in_array(gettype($levelOneTag) == 'string' ? $levelOneTag : $levelOneTag->slug, ['automotive', 'boats', 'real-estate'])) {
        return number_format($price, 0, "", ",");
    } else {
        return numberFormat($price);
    }
}
