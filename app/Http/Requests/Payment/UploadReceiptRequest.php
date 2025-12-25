<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class UploadReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'receipt_image' => [
                'required',
                'image',
                'mimes:jpeg,jpg,png',
                'max:5120', // 5MB
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'receipt_image.required' => 'L\'image du reçu est obligatoire',
            'receipt_image.image' => 'Le fichier doit être une image',
            'receipt_image.mimes' => 'L\'image doit être au format JPG ou PNG',
            'receipt_image.max' => 'L\'image ne doit pas dépasser 5MB',
        ];
    }
}
