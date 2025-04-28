<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePurchasesRequest extends FormRequest
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
            'payment_date' => 'nullable|date',
            'item' => 'nullable|string|max:255',
            'quantity' => 'nullable|integer|min:1',
            'unit' => 'nullable|string|max:255',
            'unit_price' => 'nullable|numeric|min:0.01',
            'total_price' => 'nullable|numeric|min:0.01',
            'purchase_location' => 'nullable|string|max:255',
            'user_id' => 'nullable|exists:users,id'
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'payment_date.date' => 'O formato da data de pagamento é inválido.',
            
            'item.string' => 'O nome do item deve ser um texto.',
            'item.max' => 'O nome do item não pode ter mais de 255 caracteres.',
            
            'quantity.integer' => 'A quantidade deve ser um número inteiro.',
            'quantity.min' => 'A quantidade deve ser no mínimo 1.',
            
            'unit.string' => 'A unidade de medida deve ser um texto.',
            'unit.max' => 'A unidade de medida não pode ter mais de 255 caracteres.',
            
            'unit_price.numeric' => 'O preço unitário deve ser um valor numérico.',
            'unit_price.min' => 'O preço unitário deve ser maior que zero.',
            
            'total_price.numeric' => 'O preço total deve ser um valor numérico.',
            'total_price.min' => 'O preço total deve ser maior que zero.',
            
            'purchase_location.string' => 'O local da compra deve ser um texto.',
            'purchase_location.max' => 'O local da compra não pode ter mais de 255 caracteres.',
            
            'user_id.exists' => 'O usuário informado não existe.'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
