<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
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
        $walletExistsRule = Rule::exists('wallets', 'id')->where(fn ($query) => $query->where('user_id', $this->user()->id));
        $categoryExistsRule = Rule::exists('transaction_categories', 'id')->where(
            fn ($query) => $query
                ->where('user_id', $this->user()->id)
                ->where('type', $this->input('type'))
        );

        return [
            'date' => ['required', 'date'],
            'type' => ['required', 'string', 'in:income,expense,transfer'],
            'amount' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'category_id' => ['nullable', 'required_unless:type,transfer', $categoryExistsRule],
            'wallet_id' => ['nullable', 'required_unless:type,transfer', $walletExistsRule],
            'wallet_from_id' => ['nullable', 'required_if:type,transfer', $walletExistsRule],
            'wallet_to_id' => ['nullable', 'required_if:type,transfer', 'different:wallet_from_id', $walletExistsRule],
        ];
    }

    public function prepareForValidation()
    {
        if ($this->amount !== null) {
            $normalized = preg_replace('/[^\d]/', '', (string) $this->amount);

            $this->merge([
                'amount' => $normalized === '' ? null : (int) $normalized,
            ]);
        }
    }
}
