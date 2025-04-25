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

class InvoiceMail extends Mailable implements ShouldQueue
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

        // Get shop information from config/shop.php
        $this->shopInfo = [
            'shop_name' => config('shop.name'),
            'shop_address' => config('shop.address'),
            'shop_phone' => config('shop.phone'),
            'shop_email' => config('shop.email'),
        ];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Hóa đơn #' . $this->invoice->invoice_code,
            tags: ['invoice'],
            metadata: [
                'invoice_id' => $this->invoice->id,
                'invoice_code' => $this->invoice->invoice_code,
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice',
            with: [
                'invoice' => $this->invoice,
                'shopInfo' => $this->shopInfo,
            ],
        );
    }

    /**
     * Get the message headers.
     */
    public function headers(): array
    {
        return [
            'X-Priority' => '1',
            'Importance' => 'High',
            'X-MSMail-Priority' => 'High',
        ];
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

        // Cấu hình DomPDF để hỗ trợ tiếng Việt
        $pdf->getDomPDF()->set_option('defaultFont', 'DejaVu Sans');
        $pdf->getDomPDF()->set_option('isUnicode', true);
        $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);

        // Get the invoice view content
        $html = view('admin.invoices.pdf', [
            'invoice' => $invoice,
            'shopInfo' => $this->shopInfo
        ])->render();

        $pdf->loadHTML($html);

        return [
            Attachment::fromData(fn () => $pdf->output(), "Hoa-don-{$invoice->invoice_code}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}