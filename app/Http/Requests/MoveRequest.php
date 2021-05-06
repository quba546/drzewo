<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MoveRequest extends FormRequest
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
        return [
            'moveId' => 'required|integer',
            'parentId' => 'required|integer'
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Pole jest wymagane',
            'integer' => 'Pole może zawierać tylko liczby całkowite'
        ];
    }
}
