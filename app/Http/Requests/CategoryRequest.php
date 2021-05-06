<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
            'show' => 'nullable|integer',
            'title' => 'required|alpha_num|max:50',
            'addParentId' => 'required|integer',
            'id' => 'required|integer',
            'parent_id' => 'required|integer',
            'moveId' => 'required|integer',
            'parentId' => 'required|integer',
            'editId' => 'required|integer',
            'newTitle' => 'required|alpha_num|max:50'
        ];
    }
}
