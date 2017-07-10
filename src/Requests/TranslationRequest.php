<?php

namespace GeniusTS\TranslationManager\Requests;


use GeniusTS\TranslationManager\Manager;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class TranslationRequest
 *
 * @package GeniusTS\TranslationManager
 */
class TranslationRequest extends FormRequest
{

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
        $manager = new Manager;
        $rules = [];
        $file = $this->route()->parameter('file');
        $namespace = $this->route()->parameter('namespace');

        $translation = $manager->translations($file, $namespace);

        foreach ($translation as $key => $value)
        {
            $this->generateRulesOfKey($key, $value, null, $rules);
        }

        return $rules;
    }

    /**
     * Generate rule form an element
     *
     * @param string $key
     * @param string $value
     * @param string $prefix
     * @param array $rules
     */
    protected function generateRulesOfKey($key, $value, $prefix, &$rules)
    {
        $rule = $prefix ? "{$prefix}{$key}" : $key;

        if (is_array($value))
        {
            $rules[$rule] = 'required|array';

            foreach ($value as $subKey => $subValue)
            {
                $this->generateRulesOfKey($subKey, $subValue, "{$rule}.", $rules);
            }
        }
        else
        {
            $rules[$rule] = "required|string";
        }
    }
}
