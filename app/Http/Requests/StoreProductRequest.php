<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'], // the id should exists in the categories table
            'price' => ['required', 'numeric'],
            'weight' => ['required', 'numeric'],
            'description' => ['nullable', 'string']
        ];
    }

    public function prepareForValidation()
    {
        if ($this->price) {
            $this->merge([
                'price' => $this->price * 100
            ]);
        }
    }

    public function attributes()
    {
        return [
            'category_id' => 'category'
        ];
    }
}
