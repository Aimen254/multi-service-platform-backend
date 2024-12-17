<?php

namespace App\Transformers;
use App\Transformers\Transformer;

class PublicProfileTransformer extends Transformer {

    public function transform($profile, $options = null) {
        return [
            'id' => $profile?->id,
            'user_id' => $profile?->user_id,
            'module_id' => $profile?->module_id,
            'name' => $profile?->name,
            'image' => getImage($profile?->image, 'avatar'),
            'about' => $profile?->description,
            'cover_image' => getImage($profile?->cover_image, 'banners'),
            'is_name_visible' => $profile?->is_name_visible,
            'is_public' => $profile?->is_public,
            'nick_name' => $profile?->nick_name,
            'created_at' => $profile?->created_at,
            'follower' => $profile?->followers->count() > 0 ? $profile?->followers[0]?->pivot : null,
            'followers_count' => $this->formatNumber($profile?->followers_count ? $profile?->followers_count : 0),
            'products_count' => $this->formatNumber($profile?->products_count ? $profile?->products_count : 0)
        ];

    }

    public function formatNumber($number) {
        if ($number >= 1000000000) {
            return number_format($number / 1000000000, 1) . 'B';
        } elseif ($number >= 1000000) {
            return number_format($number / 1000000, 1) . 'M';
        } elseif ($number >= 1000) {
            return number_format($number / 1000, 1) . 'K';
        }
    
        return $number;
    }
}