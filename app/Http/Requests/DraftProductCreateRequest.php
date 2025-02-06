<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Responses\ApiErrorResponse;

class DraftProductCreateRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:draft_products,name',
            'description' => 'nullable|string',
            'manufacturer' => 'required|string|max:255',
            'mrp' => 'required|numeric',
            'is_active' => 'boolean',
            'is_banned' => 'boolean',
            'is_assured' => 'boolean',
            'is_discountinued' => 'boolean',
            'is_refrigerated' => 'boolean',
            'is_published' => 'boolean',
            // 'status' => 'required|string|in:draft,pending,approved,rejected',
            'category_id' => 'required|exists:categories,id',
            'molecule_ids' => 'array|exists:molecules,id',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $response = ApiErrorResponse::create(new ValidationException($validator), 422, $validator->errors()->toArray());
        throw new ValidationException($validator, $response);
    }
}