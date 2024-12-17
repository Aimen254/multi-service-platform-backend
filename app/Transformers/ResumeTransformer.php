<?php

namespace App\Transformers;

use App\Transformers\Transformer;

class ResumeTransformer extends Transformer
{
    public function transform($resume, $options = null)
    {
        return [
            'id' => $resume->id,
            'first_name' => $resume->first_name,
            'last_name' => $resume->last_name,
            'email' => $resume->email,
            'location' => $resume->location,
            'phone' => $resume->phone,
            'experience' => $resume->experience,
            'resume' => getFileUrl($resume->resumes?->path)
        ];
    }
}
