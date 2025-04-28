<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePurchasesRequest extends FormRequest
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
            'payment_date' => 'required|date|date_format:Y-m-d',
            'item' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'unit' => 'required|string|max:255',
            'unit_price' => 'required|numeric|min:0.01',
            'total_price' => 'required|numeric|min:0.01',
            'purchase_location' => 'required|string|max:255',
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
            'payment_date.required' => 'A data de pagamento é obrigatória.',
            'payment_date.date' => 'O formato da data de pagamento é inválido. formato valido: YYYY-MM-DD',
            
            'item.required' => 'O nome do item é obrigatório.',
            'item.string' => 'O nome do item deve ser um texto.',
            'item.max' => 'O nome do item não pode ter mais de 255 caracteres.',
            
            'quantity.required' => 'A quantidade é obrigatória.',
            'quantity.integer' => 'A quantidade deve ser um número inteiro.',
            'quantity.min' => 'A quantidade deve ser no mínimo 1.',
            
            'unit.required' => 'A unidade de medida é obrigatória.',
            'unit.string' => 'A unidade de medida deve ser um texto.',
            'unit.max' => 'A unidade de medida não pode ter mais de 255 caracteres.',
            
            'unit_price.required' => 'O preço unitário é obrigatório.',
            'unit_price.numeric' => 'O preço unitário deve ser um valor numérico.',
            'unit_price.min' => 'O preço unitário deve ser maior que zero.',
            
            'total_price.required' => 'O preço total é obrigatório.',
            'total_price.numeric' => 'O preço total deve ser um valor numérico.',
            'total_price.min' => 'O preço total deve ser maior que zero.',
            
            'purchase_location.required' => 'O local da compra é obrigatório.',
            'purchase_location.string' => 'O local da compra deve ser um texto.',
            'purchase_location.max' => 'O local da compra não pode ter mais de 255 caracteres.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
