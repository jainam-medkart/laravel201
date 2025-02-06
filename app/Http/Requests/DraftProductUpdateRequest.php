<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DraftProductUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        $id = $this->route('id');

        return [
            'name' => 'required|string|max:255|unique:draft_products,name,' . $id,
            'description' => 'nullable|string',
            'manufacturer' => 'required|string|max:255',
            'mrp' => 'required|numeric',
            'is_active' => 'boolean',
            'is_banned' => 'boolean',
            'is_assured' => 'boolean',
            'is_discountinued' => 'boolean',
            'is_refrigerated' => 'boolean',
            'is_published' => 'boolean',
            'category_id' => 'required|exists:categories,id',
            'molecule_ids' => 'array|exists:molecules,id',
        ];
    }
}
