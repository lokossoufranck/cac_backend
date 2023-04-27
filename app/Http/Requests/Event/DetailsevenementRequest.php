<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DetailsevenementRequest extends FormRequest
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
            'segment_id' => 'required',
            'evenement_id' => 'required',
            'dysfonctionnement_id' =>[ 'required',
            Rule::unique('detailsevenements')
            ->where('segment_id', $this->segment_id)
            ->where('dysfonctionnement_id', $this->dysfonctionnement_id)
            ->where('evenement_id', $this->evenement_id)
                ]
            
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
            'segment_id' => 'required',
            'evenement_id' => 'required',
            'dysfonctionnement_id' =>[ 'required',
            Rule::unique('detailsevenements')
            ->ignore($this->detailsevenement)
            ->where('segment_id', $this->segment_id)
            ->where('dysfonctionnement_id', $this->dysfonctionnement_id)
            ->where('evenement_id', $this->evenement_id)
                ]
            
        ];
    }
}
