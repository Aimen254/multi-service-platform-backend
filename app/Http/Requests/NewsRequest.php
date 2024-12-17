<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NewsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $news_image = config()->get('image.media.news');
        $imageKeys = array_keys($news_image);
        $width = $news_image['width'];
        $height = $news_image['height'];
        $size = $news_image['size'];
        switch (request()->getMethod()) { 
            case 'POST':
                return [
                    'title' => 'required',
                    'news_category_id' => 'required',
                    'slug' => ['required', Rule::unique('news','slug')],
                    'image' => "sometimes|mimes:png,jpg,jpeg|max:$size|dimensions:$imageKeys[0]=$width,$imageKeys[1]=$height",
                ];
            case 'PUT':
                $id = $this->route('news');
                return [
                    'title' => 'required',
                    'news_category_id' => 'required',
                    'slug' => ['required', Rule::unique('news','slug')->ignore($id)],
                    'password' => ['nullable'],
                    'image' => "sometimes|mimes:png,jpg,jpeg|max:$size|dimensions:$imageKeys[0]=$width,$imageKeys[1]=$height",
                ];
        }
    }
}
