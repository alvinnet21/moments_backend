Konfirmasi Booking Diterima

Halo{{ !empty($book->customer_name) ? ' ' . e($book->customer_name) : '' }},

Form booking Anda sudah kami terima.

Ringkasan:
- Photografer/Videografer : {{ $positionLabel }} {{ $employeeName }}
- Tanggal                 : {{ \Illuminate\Support\Carbon::createFromTimestamp((int) $book->date)->locale('id')->translatedFormat('j F Y') }}
- Waktu                   : {{ $slotStr }}
@if(!empty($book->customer_email))
- Email                   : {{ $book->customer_email }}
@endif
@if(!empty($book->customer_phone))
- Telepon                 : {{ $book->customer_phone }}
@endif

Terima kasih.
