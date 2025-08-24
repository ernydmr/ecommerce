@component('mail::message')
# Sipariş Onayı

Merhaba **{{ $order->user->name }}**,

Sipariş numaranız: **#{{ $order->id }}**  
Toplam tutar: **₺{{ number_format($order->total_amount, 2, ',', '.') }}**

**Ürünler:**
@component('mail::table')
| Ürün | Adet | Birim Fiyat | Ara Toplam |
|:-----|:----:|------------:|-----------:|
@foreach($order->items as $item)
| {{ $item->product->name }} | {{ $item->quantity }} | ₺{{ number_format($item->price, 2, ',', '.') }} | ₺{{ number_format($item->price * $item->quantity, 2, ',', '.') }} |
@endforeach
@endcomponent

Teşekkürler, iyi alışverişler!  
{{ config('app.name') }}
@endcomponent
