<?php

namespace App\Http\Requests;

use App\Rules\MaxLengthWithoutHtml;
use App\Rules\MediaRule;
use App\Rules\ConvertTags;
use App\Models\StandardTag;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Rules\CheckAttributeTag;
use App\Rules\CountAttributeTag;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use App\Rules\PriceValidation;

class ProductRequest extends FormRequest
{
    private $width, $height, $size;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $moduleId = request()->route('moduleId');
        $module = StandardTag::where('id', $moduleId)->orWhere('slug', $moduleId)->first()->slug;
        $descriptionLength = '';
        switch ($module) {
            case 'automotive':
                $image = \config()->get('automotive.media.vehicle');
                break;
            case 'boats':
                $image = \config()->get('boats.media.boat');
                break;
            case 'taskers':
                $image = \config()->get('taskers.media.tasker');
                break;
            case 'news':
                $image = \config()->get('news.media.news');
                break;
            case 'recipes':
                $image = \config()->get('recipes.media.recipes');
                break;
            case 'blogs':
                $image = \config()->get('blogs.media.blog');
                $descriptionLength = \config()->get('blogs.description.length');
                break;
            case 'services':
                $image = \config()->get('services-module.media.services');
                break;
            case 'employment':
                $image = \config()->get('employment.media.posts');
                break;
            case 'obituaries':
                $image = \config()->get('obituaries.media.obituaries');
                break;
            case 'notices':
                $image = \config()->get('notices.media.notice');
                break;
            case 'government':
                $image = \config()->get('government.media.posts');
                $descriptionLength = \config()->get('government.description.length');
                break;
            case 'events':
                $image = \config()->get('events.media.events');
                break;
            default:
                $image = \config()->get('classifieds.media.classified');
                break;
        }
        $imageKeys = array_keys($image);
        $this->width = $image['width'];
        $this->height = $image['height'];
        $this->size = $image['size'];
        switch (request()->getMethod()) {

            case 'POST':
                //    $tagsArray= json_decode(request()->input('tags'), true);
                // //    dd(getType($tagsArray));
                //  // Replace the 'tags' input with the decoded array
                //     request()->replace(['tags' => $tagsArray]);

                return [
                    'image' => [
                        'nullable',
                        'mimes:jpeg,png,jpg',
                        "max:{$this->size}",
                        "dimensions:$imageKeys[0]={$this->width},$imageKeys[1]={$this->height}"
                    ],
                    'name' => ['nullable', Rule::requiredIf(!in_array($module, ['boats', 'notices', 'posts']))],
                    'public_profile_id' => ['nullable', Rule::requiredIf($module === 'posts')],
                    'type' => ['nullable', Rule::requiredIf($module === 'automotive' || $module === 'boats')],
                    'year' => ['nullable', Rule::requiredIf($module === 'automotive' || $module === 'boats')],
                    'event_date' => ['nullable', Rule::requiredIf($module === 'events')],
                    'performer' => ['nullable', 'string', 'max:255', Rule::requiredIf($module === 'events')],
                    'event_location' => ['nullable', 'string', 'max:255', Rule::requiredIf($module === 'events')],
                    'ticket_url' => ['nullable', 'url', Rule::requiredIf($module === 'events')],
                    'away_team' => ['nullable', 'string', 'max:255'],
                    'date_of_birth' => ['nullable', 'date', Rule::requiredIf(in_array($module, ['obituaries']))],
                    'date_of_death' => ['nullable', 'date', Rule::requiredIf(in_array($module, ['obituaries']))],
                    'stock' => ['nullable', Rule::requiredIf($module === 'retail'), 'numeric'],
                    'mileage' => ['nullable', Rule::requiredIf($module === 'automotive'), 'numeric'],
                    'vin' => ['nullable', Rule::requiredIf($module === 'automotive')],
                    'price' => ['nullable', Rule::requiredIf(in_array($module, ['automotive', 'retail', 'services', 'boats', 'events', 'employment', 'marketplace', 'real-estate'])), 'numeric', 'min:' . ($module === 'retail' ? '0.1' : '1')],
                    'max_price' => ['nullable', 'numeric', 'gt:price', 'min:1', Rule::requiredIf($module === 'events')],
                    'price_type' => ['nullable', 'string', Rule::requiredIf(in_array($module, ['taskers', 'employment']))],
                    'level_two_tag' => ['not_in:default', Rule::requiredIf($module !== 'automotive')],
                    'level_three_tag' => ['not_in:default', Rule::requiredIf($module !== 'automotive')],
                    'level_four_tags' => ['not_in:default', Rule::requiredIf($module !== 'automotive')],
                    'hierarchies.0.level_two_tag' => ['not_in:default', Rule::requiredIf($module === 'automotive')],
                    'hierarchies.0.level_three_tag' => ['not_in:default', Rule::requiredIf($module === 'automotive')],
                    'hierarchies.0.level_four_tags' => ['not_in:default', Rule::requiredIf($module === 'automotive')],
                    'hierarchies' => [Rule::requiredIf($module === 'automotive'), 'array'],
                    'hierarchies.*.level_two_tag' => [Rule::requiredIf($module === 'automotive')],
                    'hierarchies.*.level_three_tag' => [Rule::requiredIf($module === 'automotive')],
                    'hierarchies.*.level_four_tags' => [Rule::requiredIf($module === 'automotive')],
                    'description' => [
                        Rule::requiredIf(in_array($module, ['notices', 'government', 'posts'])),
                        in_array($module, ['government', 'blogs'])
                            ? new MaxLengthWithoutHtml($descriptionLength) : 'max:20000',
                        'nullable',
                        new MediaRule
                    ],
                    'sellers_notes' => ['nullable', 'max:20000', new MediaRule],
                    'is_featured' => ['nullable'],
                    'is_commentable' => ['nullable'],
                    'is_shareable' => ['nullable'],
                    'is_repostable' => ['nullable'],
                    'is_deliverable' => ['nullable'],
                    'pickup_location' => ['nullable', Rule::requiredIf(in_array($module, ['marketplace']))],
                    'cryptocurrency_accepted' => ['nullable'],
                    'user_id' => ['nullable', Rule::requiredIf(in_array($module, ['real-estate']))],
                    'business_id' => ['nullable', Rule::requiredIf(in_array($module, ['automotive', 'services', 'employment', 'boats', 'government', 'notices', 'real-estate', 'retail']) && !request()->input('user_id'))],
                    'tags.*.interior-color' => ['array', new CheckAttributeTag('interior-color'), new CountAttributeTag('interior-color')],
                    'tags.*.exterior-color' => ['array', new CheckAttributeTag('exterior-color'), new CountAttributeTag('exterior-color')],
                    'tags.*.color' => ['array', new CheckAttributeTag('color'), new CountAttributeTag('color')],
                    'tags.*.engine' => ['array', new CheckAttributeTag('engine'), new CountAttributeTag('engine')],
                    'tags.*.fuel-type' => ['array', new CheckAttributeTag('fuel-type'), new CountAttributeTag('fuel-type')],
                    'tags.*.condition' => ['array', new CheckAttributeTag('condition'), new CountAttributeTag('condition')],
                    'tags.*.bed' => ['array', new CheckAttributeTag('bed'), new CountAttributeTag('bed')],
                    'tags.*.bath' => ['array', new CheckAttributeTag('bath'), new CountAttributeTag('bath')],
                    'square_feet' => [Rule::requiredIf(in_array($module, ['real-estate'])), Rule::when($module === 'real-estate', ['regex:/^\d+x\d+$/']),],
                    'tags.*.garage-capacity' => ['array', new CountAttributeTag('garage-capacity')],
                    'tags.*.listing-type' => ['array', new CountAttributeTag('listing-type')],
                ];
            case 'PUT':

                // for vehicle basic information
                return [
                    'image' => [
                        'nullable',
                        'mimes:jpeg,png,jpg',
                        "max:{$this->size}",
                        "dimensions:$imageKeys[0]={$this->width},$imageKeys[1]={$this->height}"
                    ],
                    'name' => ['nullable', Rule::requiredIf(!in_array($module, ['boats', 'notices', 'posts']))],
                    'public_profile_id' => ['nullable', Rule::requiredIf($module === 'posts')],

                    'type' => ['nullable', Rule::requiredIf($module === 'automotive' || $module === 'boats')],
                    'year' => ['nullable', Rule::requiredIf($module === 'automotive' || $module === 'boats')],
                    'ticket_url' => ['nullable', 'url', Rule::requiredIf($module === 'events')],
                    'event_date' => ['nullable', Rule::requiredIf($module === 'events')],
                    'performer' => ['nullable', 'string', 'max:255', Rule::requiredIf($module === 'events')],
                    'event_location' => ['nullable', 'string', 'max:255', Rule::requiredIf($module === 'events')],
                    'stock' => ['nullable', Rule::requiredIf($module === 'retail'), 'numeric'],
                    'date_of_birth' => ['nullable', Rule::requiredIf(in_array($module, ['obituaries']))],
                    'date_of_death' => ['nullable', Rule::requiredIf(in_array($module, ['obituaries']))],
                    // 'condition' => ['nullable', Rule::requiredIf($module === 'marketplace')],
                    'mileage' => ['nullable', Rule::requiredIf($module === 'automotive'), 'numeric'],
                    'vin' => ['nullable', Rule::requiredIf($module === 'automotive')],
                    'price' => ['nullable', Rule::requiredIf(in_array($module, ['automotive', 'retail', 'services', 'boats', 'events', 'employment', 'marketplace', 'real-estate'])), 'numeric', 'min:' . ($module === 'retail' ? '0.1' : '1')],
                    'max_price' => ['nullable', 'numeric', 'gt:price', 'min:1', Rule::requiredIf($module === 'events')],
                    'price_type' => ['nullable', 'string', Rule::requiredIf(in_array($module, ['services', 'taskers', 'employment']))],
                    'away_team' => ['nullable', 'string', 'max:255'],
                    'level_two_tag' => 'not_in:default',
                    'level_three_tag' => 'not_in:default',
                    'level_four_tags' => 'not_in:default',
                    'hierarchies.0.level_two_tag' => 'not_in:default',
                    'hierarchies.0.level_three_tag' => 'not_in:default',
                    'hierarchies.0.level_four_tags' => 'not_in:default',
                    'is_featured' => ['nullable'],
                    'is_commentable' => ['nullable'],
                    'is_shareable' => ['nullable'],
                    'is_repostable' => ['nullable'],
                    'is_deliverable' => ['nullable'],
                    'pickup_location' => ['nullable', Rule::requiredIf(in_array($module, ['marketplace']))],
                    'cryptocurrency_accepted' => ['nullable'],
                    'hierarchies.*.level_two_tag' => [Rule::requiredIf($module === 'automotive')],
                    'hierarchies.*.level_three_tag' => [Rule::requiredIf($module === 'automotive')],
                    'hierarchies.*.level_four_tags' => [Rule::requiredIf($module === 'automotive')],
                    'description' => [
                        Rule::requiredIf(in_array($module, ['notices', 'government', 'posts'])),
                        in_array($module, ['government', 'blogs'])
                            ? new MaxLengthWithoutHtml($descriptionLength) : 'max:20000',
                        'nullable',
                        new MediaRule
                    ],
                    'sellers_notes' => ['nullable', 'max:20000', new MediaRule],
                    'business_id' => ['nullable', Rule::requiredIf(in_array($module, ['automotive', 'services', 'employment', 'boats', 'government', 'notices', 'real-estate', 'retail']) && !request()->input('user_id'))],
                    'tags.*.interior-color' => ['array', new CheckAttributeTag('interior-color'), new CountAttributeTag('interior-color')],
                    'tags.*.exterior-color' => ['array', new CheckAttributeTag('exterior-color'), new CountAttributeTag('exterior-color')],
                    'tags.*.color' => ['array', new CheckAttributeTag('color'), new CountAttributeTag('color')],
                    'tags.*.engine' => ['array', new CheckAttributeTag('engine'), new CountAttributeTag('engine')],
                    'tags.*.fuel-type' => ['array', new CheckAttributeTag('fuel-type'), new CountAttributeTag('fuel-type')],
                    'tags.*.condition' => ['array', new CheckAttributeTag('condition'), new CountAttributeTag('condition')],
                    'tags.*.bed' => ['array', new CheckAttributeTag('bed'), new CountAttributeTag('bed')],
                    'tags.*.bath' => ['array', new CheckAttributeTag('bath'), new CountAttributeTag('bath')],
                    'square_feet' => [Rule::requiredIf(in_array($module, ['real-estate'])), Rule::when($module === 'real-estate', ['regex:/^\d+x\d+$/']),],
                    'tags.*.garage-capacity' => ['array', new CountAttributeTag('garage-capacity')],
                    'tags.*.listing-type' => ['array', new CountAttributeTag('listing-type')],
                ];
        }
    }

    protected function prepareForValidation()
    {
        if ($this->input('tags')) {
            $tagsArray = json_decode($this->input('tags'), true);
            $this->merge(['tags' => $tagsArray]);
        }
        if ($this->input('hierarchies')) {
            $hierarchiesArray = json_decode($this->input('hierarchies'), true);
            $this->merge(['hierarchies' => $hierarchiesArray]);
        }
        if ($this->input('price')) {
            $price = $this->input('price');
            // Remove commas and format as float
            $price = floatval(str_replace(',', '', $price));
            // Merge the modified price back into the request
            $this->merge(['price' => $price]);
            // Now check the type of the input
        }

        if ($this->input('max_price')) {
            $max_price = $this->input('max_price');
            // Remove commas and format as float
            $max_price = floatval(str_replace(',', '', $max_price));
            // Merge the modified price back into the request
            $this->merge(['max_price' => (float)$max_price]);
            // Now check the type of the input
        }
    }


    public function messages()
    {

        $message = '';
        $businessMessage = '';
        $moduleId = request()->route('moduleId');
        $squareFeetRequiredMessage = '';
        $squareFeetFormatMessage = '';
        $module = StandardTag::where('id', $moduleId)->orWhere('slug', $moduleId)->first()->slug;
        switch ($module) {
            case 'employment':
                $message = 'The salary field is required';
                $businessMessage = 'The employer field is required';
                break;
            case 'services':
                $message = 'The budget field is required';
                $businessMessage = 'The provider field is required';
                break;
            case 'automotive':
            case 'boats':
                $message = 'The price field is required';
                $businessMessage = 'The dealership field is required';
                break;
            case 'real-estate':
                $message = 'The price field is required';
                $businessMessage = 'The broker field is required';
                $squareFeetRequiredMessage = 'The square feet field is required.';
                $squareFeetFormatMessage = 'The square feet must be in the format "numberxnumber" (e.g., "200x400").';
                break;
            case 'events':
                $message = 'The minimum price field is required.';
                $businessMessage = 'The maximum price field is required.';
                break;
            default:
                $message = 'The price field is required';
                $businessMessage = 'The business field is required';
        }
        return [
            'image.max' => "Image size must be {$this->size} kb",
            'image.dimensions' => "Image dimensions must be of {$this->width} x {$this->height}",
            'price.required' => $message,
            'business_id.required' => $businessMessage,
            'user_id.required' => 'The agent field is required',
            'max_price.required' => $businessMessage,
            'event_date.required' => "The event date and time field is required.",
            'hierarchies.0.level_two_tag.required' => 'Level two tag is required',
            'hierarchies.0.level_three_tag.required' => 'Level three tag is required',
            'hierarchies.0.level_four_tags.required' => 'Level four tag is required',
            'public_profile_id.required' => 'A public profile is required when creating posts.',
            'square_feet.required' => $squareFeetRequiredMessage,
            'square_feet.regex' => $squareFeetFormatMessage,
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $routeName = $this->route()->getName();
        if (strpos($routeName, 'products.store') !== false || strpos($routeName, 'products.update') !== false) {
            $response = new JsonResponse([
                'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY,);
            throw new ValidationException($validator, $response);
        }
        // use default behavior for web.php routes
        parent::failedValidation($validator);
    }
}
