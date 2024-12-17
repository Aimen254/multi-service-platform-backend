<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Product;
use App\Models\Business;
use App\Models\StandardTag;
use Illuminate\Support\Arr;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Modules\Retail\Entities\SubscriptionPermission;

trait StripeSubscription
{
    public $modules = [];

    public function checkActiveBusinesses(Business $business, $type, $moduleId = null)
    {
        try {
            //business Owner
            $businessOwner = $business->businessOwner;
            //business owner active businesses
            $activeBusinesses = $businessOwner->businesses()
                ->whereStatus('active')
                ->whereNull('deleted_at')
                ->when($moduleId, function ($query) use ($moduleId) {
                    $query->where(function ($query) use ($moduleId) {
                        $query->whereHas('standardTags', function ($query) use ($moduleId) {
                            $query->where('id', $moduleId);
                        });
                    });
                })->count();
            //business owner featured businesses
            $featuredBusinesses = $businessOwner->businesses()->whereIsFeatured(1)->count();
            //get matched subscription
            $matchedSubscription = $this->matchedSubscription($business);
            if (!$matchedSubscription) {
                return true;
            }
            //getting permissions which subscription contains.
            $totalAllowedBusinessesStatus = SubscriptionPermission::where('product_id', $matchedSubscription->plan->product)->where('key', 'total_businesses')->first()->status;
            if ($totalAllowedBusinessesStatus) {
                $totalAllowedBusinesses = SubscriptionPermission::where('product_id', $matchedSubscription->plan->product)->where('key', 'total_businesses')->firstOrFail()->value;
            } else {
                $totalAllowedBusinesses = 0;
            }
            $totalFeaturedBusinessesStatus = SubscriptionPermission::where('product_id', $matchedSubscription->plan->product)->where('key', 'featured_businesses')->first()->status;
            if ($totalFeaturedBusinessesStatus) {
                $totalAllowedFeaturedBusinesses = SubscriptionPermission::where('product_id', $matchedSubscription->plan->product)->where('key', 'featured_businesses')->firstOrFail()->value;
            } else {
                $totalAllowedFeaturedBusinesses = 0;
            }
            switch ($type) {
                case 'check_active_businesses':
                    //comparing active businesses and total allowed businesses
                    if ((int)$totalAllowedBusinesses == -1) {
                        return true;
                    } else if ((int)$totalAllowedBusinesses <= $activeBusinesses) {
                        return false;
                    } else {
                        return true;
                    }
                    break;
                case 'check_featured_businesses':
                    //comparing featured businesses and total allowed featured businesses
                    if ((int)$totalAllowedFeaturedBusinesses == -1) {
                        return true;
                    } else if ((int)$totalAllowedFeaturedBusinesses <= $featuredBusinesses) {
                        return false;
                    } else {
                        return true;
                    }
                    break;
            }
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }

    public function checkAllowedModules()
    {
        try {
            //user subscriptions
            if (request()->user()->stripe_customer_id) {
                $subscription = $this->stripeClient->subscriptions->all([
                    'customer' => request()->user()->stripe_customer_id,
                    'limit' => 100,
                ]);
                collect($subscription->data)->each(function ($sub, $key) {
                    if ($sub->status == 'active') {
                        array_push($this->modules, $sub->metadata->module);
                    }
                });
                // adding automotive in subscribed modules
                // array_push($this->modules, 'automotive');
            }
            return $this->modules;
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }

    public function checkAllowedProducts($business)
    {
        //business total products count
        $totalProducts = $business->products()->whereStatus('active')->count();
        //get matched subscription
        $matchedSubscription = $this->matchedSubscription($business);
        
        if (!$matchedSubscription) {
            return true;
        }

        if ($matchedSubscription) {
            $totalAllowedProductsStatus = SubscriptionPermission::where('product_id', $matchedSubscription->plan->product)->where('key', 'total_products')->first();
            $totalAllowedProductsStatus = $totalAllowedProductsStatus ? $totalAllowedProductsStatus->status : null;
            if ($totalAllowedProductsStatus) {
                $totalAllowedProducts = SubscriptionPermission::where('product_id', $matchedSubscription->plan->product)->where('key', 'total_products')->firstOrFail()->value;
            } else {
                $totalAllowedProducts = 0;
            }
        } else {
            $totalAllowedProducts = 0;
        }
        // getting total allowed products
        //comparing total products and total allowed products
        if ((int)$totalAllowedProducts == -1) {
            return true;
        } else if ((int)$totalAllowedProducts <= $totalProducts) {
            return false;
        } else {
            return true;
        }
    }

    public function matchedSubscription($business)
    {
        $businessOwner = $business->businessOwner;
        //module of business
        $module = $business->standardTags()->whereType('module')->firstOrFail()->slug;
        //user subscriptions
        $subscription = $this->stripeClient->subscriptions->all([
            'customer' => $businessOwner->stripe_customer_id,
            'limit' => 100,
        ]);


        //getting subscription matched with module
        $matchedSubscription = collect($subscription->data)->filter(function ($sub, $key) use ($module) {
            return $sub->metadata->module == $module;
        })->first();

        return $matchedSubscription;
    }

    public function checkDeliveryPermission($business)
    {
        $matchedSubscription = $this->matchedSubscription($business);
        if ($matchedSubscription) {
            # code...
            $deliveryPermission = SubscriptionPermission::where('product_id', $matchedSubscription->plan->product)->where('key', 'delivery')->firstOrFail()->status;
            return $deliveryPermission ? true : false;
        }
    }

    public function updateUserMeta($productId, $module)
    {
        $moduleTags = StandardTag::whereSlug($module)->firstOrFail();
        $subscription_type = SubscriptionPermission::where('product_id', $productId)->where('key', 'type')->first();
        if (request()->user()->stripe_customer_id) {
            $customer = $this->stripeClient->customers->retrieve(
                request()->user()->stripe_customer_id,
                []
            );
            $customerMeta = $customer->metadata->toArray();
            $flag = array_key_exists($module, $customerMeta);
            if ($flag) {
                $customerMeta[$module] = $subscription_type->value;
            } else {
                $customerMeta = Arr::add($customerMeta, $module, $subscription_type->value);
            }
            $user = $this->stripeClient->customers->update(
                request()->user()->stripe_customer_id,
                ['metadata' => $customerMeta]
            );
            //updating user_subscription table
            $user_subscription = UserSubscription::updateOrCreate(
                ['user_id' => request()->user()->id, 'module_id' => $moduleTags->id],
                ['product_id' => $productId],
            );
        }
    }

    public function getSubscriptionCustomers($level, $module)
    {
        try {
            $moduleTags = StandardTag::whereSlug($module)->firstOrFail();
            $customerIds = User::whereHas('subscriptions', function ($query) use ($moduleTags, $level) {
                $query->where('module_id', $moduleTags->id)
                    ->whereHas('subscriptionPermissions', function ($subQuery) use ($level) {
                        $subQuery->where('key', 'type')->where('value', $level);
                    });
            })->pluck('stripe_customer_id')->toArray();
            return $customerIds;
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }

        //Old code saved for future use
        // $customerIds = [];
        // $customers = $this->stripeClient->customers->search([
        //     'limit' => 100,
        //     'query' => 'metadata[\''.$module.'\']:\''.$level.'\'',
        // ]);
        // $allCustomers = collect($customers)['data'];
        // if (count($allCustomers) > 0) {
        //     foreach ($allCustomers as $key => $customer) {
        //         array_push($customerIds, $customer['id']);
        //     }
        // }
        // return $customerIds;
    }

    public function checkGrantedProducts($moduleId, $userId): bool
    {
        // count total products on user based
        $totalProducts = Product::moduleBasedProducts($moduleId)->where('user_id', $userId)->whereStatus('active')->count();
        $matchedSubscription = $this->compareSubscription($moduleId, $userId);
        if ($matchedSubscription) {
            $totalAllowedProductsStatus = SubscriptionPermission::where('product_id', $matchedSubscription->plan->product)->where('key', 'total_products')->first();
            $totalAllowedProductsStatus = $totalAllowedProductsStatus ? $totalAllowedProductsStatus->status : null;
            if ($totalAllowedProductsStatus) {
                $totalAllowedProducts = SubscriptionPermission::where('product_id', $matchedSubscription->plan->product)->where('key', 'total_products')->firstOrFail()->value;
            } else {
                $totalAllowedProducts = 0;
            }
        } else {
            $totalAllowedProducts = 0;
        }

        /**
         * getting total granted products comparing
         * total products and total granted products
         */
        if ((int)$totalAllowedProducts == -1) {
            return true;
        } else if ((int)$totalAllowedProducts <= $totalProducts) {
            return false;
        } else {
            return true;
        }
    }

    public function compareSubscription($moduleId, $userId)
    {
        $user = User::findOrFail($userId);
        $module = StandardTag::whereId($moduleId)->whereType('module')->firstOrFail()->slug;
        //user subscriptions
        $subscription = $this->stripeClient->subscriptions->all([
            'customer' => $user->stripe_customer_id,
            'limit' => 100,
        ]);
        //getting subscription matched with module
        $matchedSubscription = collect($subscription->data)->filter(function ($sub, $key) use ($module) {
            return $sub->metadata->module == $module;
        })->first();

        return $matchedSubscription;
    }
}
