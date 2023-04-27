<?php

namespace App\Http\Requests\Module;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Profile_userRequest extends FormRequest
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
            'user_id' =>  [
                'required', 
                Rule::unique('profile_user')
                       ->where('user_id', $this->user_id)
                       ->where('profile_id', $this->profile_id)
                       
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
            'user_id' =>  [
                'required', 
                Rule::unique('profile_user')
                ->ignore($this->profile)
                ->where('user_id', $this->user_id)
                ->where('profile_id', $this->profile_id)
                ->where('actif',  $this->actif ? 1 : 0)
            ],
            'actif' => 'required'

        ];
    }
}
