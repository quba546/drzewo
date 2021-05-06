<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'title' => 'required|alpha_num|max:50',
            'addParentId' => 'required|integer'
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Pole jest wymagane',
            'alpha_num' => 'Pole może zawierać tylko litery i cyfry',
            'max' => 'Pole może zawierać maksymalnie :max znaków',
            'integer' => 'Pole może zawierać tylko liczby całkowite'
        ];
    }
}
