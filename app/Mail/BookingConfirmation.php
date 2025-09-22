<?php

namespace App\Mail;

use App\Models\Book;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;

class BookingConfirmation extends Mailable
{
    public function __construct(public Book $book)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: 'Konfirmasi: Form booking Anda telah diterima'
        );
    }

    public function content(): Content
    {
        $slotStr = is_string($this->book->time_slot)
            ? $this->book->time_slot
            : ($this->book->time_slot->value ?? (string) $this->book->time_slot);
        $logoUrl = env('MAIL_LOGO_URL');
        if (empty($logoUrl)) {
            $logoUrl = url('/logo.png');
        }
        $logoPath = null;
        $projectLogo = base_path('logo.png');
        if (is_file($projectLogo)) {
            $logoPath = $projectLogo;
        }

        $employee = $this->book->employee;
        $employeeName = $employee?->name ?? '-';
        $positionEnum = $employee?->position;
        $positionVal = is_string($positionEnum) ? $positionEnum : ($positionEnum->value ?? '');
        $positionLabel = match ($positionVal) {
            'PHOTOGRAPHER' => 'Photografer',
            'VIDEOGRAPHER' => 'Videografer',
            default => 'Petugas',
        };

        return new Content(
            view: 'emails.booking_confirmation',
            text: 'emails.booking_confirmation_plain',
            with: [
                'book' => $this->book,
                'slotStr' => $slotStr,
                // Prefer inline-embedded logo if available; otherwise fall back to URL
                'logoPath' => $logoPath,
                'logoUrl' => $logoUrl,
                'employeeName' => $employeeName,
                'positionLabel' => $positionLabel,
            ],
        );
    }
}
