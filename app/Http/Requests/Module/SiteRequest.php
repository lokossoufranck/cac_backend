<?php

namespace App\Http\Requests\Module;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SiteRequest extends FormRequest
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
        if ($this->isMethod('post')) {
            return $this->createRules();
        } elseif ($this->isMethod('put')) {
            return $this->updateRules();
        }
    }

    /**
     * Define validation rules to store method for resource creation
     *
     * @return array
     */
    public function createRules(): array
    {
        return [
            'nom' =>  [
                'required', 
                Rule::unique('profiles')
                       ->where('nom', $this->nom)
                       
            ],
            'tel1' => 'required',
            'pays_id' => 'required',
            'actif' => 'required'

        ];
    }

    /**
     * Define validation rules to update method for resource update
     *
     * @return array
     */
    public function updateRules(): array
    {
        return [
            'nom' =>  [
                'required', 
                Rule::unique('profiles')
                ->ignore($this->profile)
                ->where('nom', $this->nom)
            ],
            'tel1' => 'required',
            'pays_id' => 'required',
            'actif' => 'required'

        ];
    }
}
