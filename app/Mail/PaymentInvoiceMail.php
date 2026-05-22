<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $invoiceNumber;
    public string $userName;
    public string $userEmail;
    public string $itemType;   // 'event' | 'course'
    public string $itemTitle;
    public float  $amount;
    public string $paymentMethod;
    public string $paidAt;
    public string $orderId;

    public function __construct(
        string $invoiceNumber,
        string $userName,
        string $userEmail,
        string $itemType,
        string $itemTitle,
        float  $amount,
        string $paymentMethod,
        string $paidAt,
        string $orderId
    ) {
        $this->invoiceNumber = $invoiceNumber;
        $this->userName      = $userName;
        $this->userEmail     = $userEmail;
        $this->itemType      = $itemType;
        $this->itemTitle     = $itemTitle;
        $this->amount        = $amount;
        $this->paymentMethod = $paymentMethod;
        $this->paidAt        = $paidAt;
        $this->orderId       = $orderId;
    }

    public function build()
    {
        $subject = 'Invoice Pembayaran #' . $this->invoiceNumber . ' – ' . $this->itemTitle;

        // Get logo base64 for embedding in PDF
        $logoPath = public_path('aset/logo idspora_dark.png');
        $logoSrc = '';
        if (file_exists($logoPath)) {
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoSrc = 'data:image/png;base64,' . $logoData;
        }

        $pdfData = [
            'invoiceNumber' => $this->invoiceNumber,
            'userName'      => $this->userName,
            'userEmail'     => $this->userEmail,
            'itemType'      => $this->itemType,
            'itemTitle'     => $this->itemTitle,
            'amount'        => $this->amount,
            'paymentMethod' => $this->paymentMethod,
            'paidAt'        => $this->paidAt,
            'orderId'       => $this->orderId,
            'logoSrc'       => $logoSrc,
            'isPdf'         => true,
        ];

        // Generate PDF using Dompdf
        $html = view('emails.payment-invoice', $pdfData)->render();

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Helvetica');
        
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $pdfOutput = $dompdf->output();

        return $this
            ->subject($subject)
            ->view('emails.payment-invoice')
            ->with([
                'invoiceNumber' => $this->invoiceNumber,
                'userName'      => $this->userName,
                'userEmail'     => $this->userEmail,
                'itemType'      => $this->itemType,
                'itemTitle'     => $this->itemTitle,
                'amount'        => $this->amount,
                'paymentMethod' => $this->paymentMethod,
                'paidAt'        => $this->paidAt,
                'orderId'       => $this->orderId,
            ])
            ->attachData($pdfOutput, 'Invoice-' . $this->invoiceNumber . '.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
