<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DysfonctionnementRequest extends FormRequest
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
                Rule::unique('dysfonctionnements')
                       ->where('campagne_id', $this->campagne_id)
                       ->where('porte_id', $this->porte_id)
                       
            ],
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
                Rule::unique('dysfonctionnements')
                ->ignore($this->dysfonctionnement)
                ->where('campagne_id', $this->campagne_id)
                ->where('porte_id', $this->porte_id)
             ],
            'actif' => 'required'

        ];
    }
}
