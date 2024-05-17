<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeliveryPurchaseReceiptRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {

        // check if the user is an admin
        if (auth()->user()->hasRole('admin')) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'dr_no' => 'required|unique:delivery_purchase_receipts,dr_no',
            'issue_date' => 'required|date',
            'user_id' => 'required|exists:users,id',
        ];
    }
}
