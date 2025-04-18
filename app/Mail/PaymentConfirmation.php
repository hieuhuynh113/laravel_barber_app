<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Appointment;
use App\Models\PaymentReceipt;

class PaymentConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The appointment instance.
     *
     * @var \App\Models\Appointment
     */
    public $appointment;

    /**
     * The payment receipt instance.
     *
     * @var \App\Models\PaymentReceipt
     */
    public $receipt;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\Appointment $appointment
     * @param \App\Models\PaymentReceipt $receipt
     */
    public function __construct(Appointment $appointment, PaymentReceipt $receipt)
    {
        $this->appointment = $appointment;
        $this->receipt = $receipt;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Xác nhận thanh toán - Mã đặt lịch: ' . $this->appointment->booking_code,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-confirmation',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
