@component('mail::message')
# Sipariş Onayı

Merhaba {{ $user->name }},

Siparişiniz oluşturuldu. Detaylar:

**Sipariş No:** #{{ $order->id }}  
**Durum:** {{ $order->status }}  
**Tutar:** ₺{{ number_format((float)$order->total_amount, 2, ',', '.') }}

@component('mail::table')
| Ürün | Adet | Birim Fiyat | Ara Toplam |
|:-----|----:|------------:|-----------:|
@foreach($items as $it)
@php($p = $it->product)
| {{ $p?->name ?? "Ürün #{$it->product_id}" }} 
| {{ $it->quantity }} 
| ₺{{ number_format((float)$it->price, 2, ',', '.') }} 
| ₺{{ number_format((float)$it->price * $it->quantity, 2, ',', '.') }} |
@endforeach
@endcomponent

Teşekkürler,  
{{ config('app.name') }}
@endcomponent
