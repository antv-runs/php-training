<?php

namespace App\Http\Requests;

class OrderRequest extends BaseApiRequest
{
    public function authorize()
    {
        // only authenticated users can place orders
        return auth()->check();
    }

    public function rules()
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages()
    {
        return [
            'items.required' => 'You must add at least one item to the order.',
            'items.*.product_id.exists' => 'Selected product does not exist.',
        ];
    }
}
