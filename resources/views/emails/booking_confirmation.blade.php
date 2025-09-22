<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking Confirmation</title>
    <style>
      /* Basic, email-friendly styles */
      body { margin: 0; padding: 0; background: #f6f8fb; font-family: Arial, Helvetica, sans-serif; color: #111827; }
      .container { width: 100%; background: #f6f8fb; padding: 24px 0; }
      .card { max-width: 640px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.06); }
      .header { padding: 16px 24px; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: 12px; background: #998c7f; }
      .brand { font-size: 16px; font-weight: 600; color: #ffffff; margin-left: auto; }
      .logo { height: 28px; filter: grayscale(1) brightness(0) invert(1); }
      .content { padding: 24px; line-height: 1.5; }
      .title { font-size: 18px; font-weight: 700; margin: 0 0 8px; }
      .muted { color: #6b7280; font-size: 14px; margin: 0 0 16px; }
      .details { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 16px; }
      .row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px dashed #e5e7eb; }
      .row:last-child { border-bottom: 0; }
      .label { color: #6b7280; font-size: 14px; padding-right: 4px; }
      .value { color: #111827; font-size: 14px; font-weight: 600; }
      .footer { padding: 16px 24px; border-top: 1px solid #e5e7eb; text-align: center; color: #9ca3af; font-size: 12px; }
      @media (max-width: 480px) { .row { display: block; } .value { margin-top: 2px; display: block; } }
    </style>
  </head>
  <body>
    <div class="container">
      <div class="card">
        <div class="header">
          @if(!empty($logoPath))
            <img class="logo" src="{{ $message->embed($logoPath) }}" alt="Logo" />
          @elseif(!empty($logoUrl))
            <img class="logo" src="{{ $logoUrl }}" alt="Logo" />
          @endif
          <div class="brand">Booking Confirmation</div>
        </div>
        <div class="content">
          <h1 class="title">Konfirmasi Booking Diterima</h1>
          <p class="muted">Halo{{ !empty($book->customer_name) ? ' ' . e($book->customer_name) : '' }}, form booking Anda telah kami terima. Berikut ringkasannya:</p>

          <div class="details">
            <div class="row"><div class="label">{{ $positionLabel }}</div><div class="value">{{ $employeeName }}</div></div>
            <div class="row"><div class="label">Tanggal</div><div class="value">{{ \Illuminate\Support\Carbon::createFromTimestamp((int) $book->date)->locale('id')->translatedFormat('j F Y') }}</div></div>
            <div class="row"><div class="label">Waktu</div><div class="value">{{ $slotStr }}</div></div>
            @if(!empty($book->customer_email))
              <div class="row"><div class="label">Email</div><div class="value">{{ $book->customer_email }}</div></div>
            @endif
            @if(!empty($book->customer_phone))
              <div class="row"><div class="label">Telepon</div><div class="value">{{ $book->customer_phone }}</div></div>
            @endif
          </div>

          <p style="margin-top:16px">Terima kasih telah melakukan booking. Kami akan segera memproses permintaan Anda.</p>
        </div>
        <div class="footer">
          Email ini dikirim otomatis—harap tidak membalas email ini.
        </div>
      </div>
    </div>
  </body>
  </html>
