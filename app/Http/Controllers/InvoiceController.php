<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ManualPayment;
use App\Models\Course;
use App\Models\Event;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{

    /**
     * Download Invoice for a Manual Payment
     */
    public function manualInvoice($order_id)
    {
        $payment = ManualPayment::where('order_id', $order_id)->firstOrFail();

        // Security check
        if (Auth::user()->role !== 'admin' && Auth::id() !== $payment->user_id) {
            abort(403);
        }

        if ($payment->status !== 'settled') {
            return back()->with('error', 'Invoice hanya tersedia untuk pembayaran yang sudah dikonfirmasi.');
        }

        $itemName = $this->getItemName($payment);
        $user = $payment->user;

        return $this->generatePdf($payment, $itemName, $user, 'Manual Transfer');
    }

    private function getItemName($payment)
    {
        if ($payment->course_id) {
            return Course::find($payment->course_id)?->title ?? 'Course';
        }
        if ($payment->event_id) {
            return Event::find($payment->event_id)?->title ?? 'Event';
        }
        return 'Spora Service';
    }

    private function generatePdf($payment, $itemName, $user, $method)
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        $data = [
            'payment' => $payment,
            'itemName' => $itemName,
            'user' => $user,
            'method' => $method,
            'date' => $payment->created_at->format('d F Y'),
            'invoice_no' => 'INV-' . strtoupper(substr($payment->order_id, 0, 8))
        ];

        $html = view('invoices.template', $data)->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="Invoice_' . $payment->order_id . '.pdf"');
    }
}
