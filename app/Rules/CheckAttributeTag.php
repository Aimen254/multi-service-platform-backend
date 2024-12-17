<?php

namespace App\Rules;

use App\Models\Attribute;
use App\Models\StandardTag;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Validation\Rule;

class CheckAttributeTag implements Rule
{
    /**
     * The attribute key that does not exist.
     *
     * @var string|null
     */
    private $nonexistentKey;
    protected $attributeName = null;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($attributeName)
    {
        $this->attributeName = $attributeName;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $moduleId = request()->route('moduleId');
        $module = StandardTag::where('id', $moduleId)->orWhere('slug', $moduleId)->first();
        $requiredAttributes = ['interior-color', 'exterior-color'];

        switch ($module?->slug) {
            case 'marketplace':
                $requiredAttributes = ['condition', 'color'];
                break;
            case 'boats':
                $requiredAttributes = ['color', 'engine', 'fuel-type'];
                break;
            case 'real-estate':
                $requiredAttributes = ['bed', 'bath'];
                break;
            case 'events':
                $requiredAttributes = ['event-location'];
                break;
            default:
                $requiredAttributes = ['interior-color', 'exterior-color'];
                break;
        }

        if (Attribute::where('slug', $this->attributeName)->whereRelation('moduleTags', 'id', $module->id)->exists() && (in_array($this->attributeName, $requiredAttributes))) {
            return count($value) > 0  ? true : false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "The {$this->attributeName} attribute is required.";
    }

    public function filterAttribute($key, $tags)
    {
        $keyExists = collect($tags)->pluck('attribute')->flatten(1)->pluck('slug')->contains($key);
        return $keyExists ? null : $key;
    }
}
