@component('mail::message')
# Order #{{ $order->id }} Confirmation

Thank you for your purchase! Here are the items you ordered:

@foreach($order->items as $item)
- {{ $item->product->name }} x {{ $item->quantity }} — ${{ $item->total }}
@endforeach

**Total amount:** ${{ $order->total_amount }}

Thanks for shopping with us!

Regards,<br>
{{ config('app.name') }}
@endcomponent
