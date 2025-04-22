<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use App\Models\Invoice;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The invoice instance.
     *
     * @var \App\Models\Invoice
     */
    public $invoice;

    /**
     * The shop information.
     *
     * @var array
     */
    public $shopInfo;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\Invoice $invoice
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
        
        // Get shop information from settings or config
        $this->shopInfo = [
            'shop_name' => config('app.name', 'Barber Shop'),
            'shop_address' => config('shop.address', '123 Barber Street'),
            'shop_phone' => config('shop.phone', '0123456789'),
            'shop_email' => config('shop.email', 'contact@barbershop.com'),
        ];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Hóa đơn #' . $this->invoice->invoice_code,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $invoice = $this->invoice;
        $pdf = app('dompdf.wrapper');
        
        // Get the invoice view content
        $html = view('admin.invoices.pdf', [
            'invoice' => $invoice,
            'shopInfo' => $this->shopInfo
        ])->render();
        
        $pdf->loadHTML($html);
        
        return [
            Attachment::fromData(fn () => $pdf->output(), "Invoice-{$invoice->invoice_code}.pdf")
                ->withMime('application/pdf'),
        ];
    }
} 