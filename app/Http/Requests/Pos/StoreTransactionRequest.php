<?php

namespace App\Http\Requests\Pos;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->canAccessPos();
    }

    public function rules(): array
    {
        return [
            'outlet_id' => 'required|exists:outlets,id',
            'shift_id' => 'required|exists:pos_shifts,id',
            'customer_id' => 'nullable|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'items.*.tax_amount' => 'nullable|numeric|min:0',
            'items.*.total_amount' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,ewallet,qris,split',
            'payment_details' => 'nullable|array',
            'cash_received' => 'required_if:payment_method,cash|numeric|min:0',
            'change_amount' => 'nullable|numeric|min:0',
            'coupon_code' => 'nullable|string',
            'notes' => 'nullable|string|max:500',
            'payments' => 'required_if:payment_method,split|array',
            'payments.*.method' => 'required_with:payments|in:cash,card,ewallet,qris',
            'payments.*.amount' => 'required_with:payments|numeric|min:0',
            'payments.*.details' => 'nullable|array',
            'payments.*.reference_number' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Transaction must have at least one item',
            'items.min' => 'Transaction must have at least one item',
            'cash_received.required_if' => 'Cash received is required for cash payment',
            'payments.required_if' => 'Payment details are required for split payment',
        ];
    }
}
