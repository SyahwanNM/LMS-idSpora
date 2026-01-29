<?php

namespace App\Http\Controllers;

use App\Models\SupportMessage;
use Illuminate\Http\Request;

class PublicPagesController extends Controller
{


    public function support()
    {
        return view('public.support');
    }

    public function storeSupport(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:255',
            'type' => 'required|in:pertanyaan,kendala,masukan,lainnya',
            'subject' => 'required|string|max:150',
            'message' => 'required|string|max:3000',
        ]);

        SupportMessage::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'type' => $validated['type'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'new',
        ]);

        return back()->with('success', 'Terima kasih! Pesan Anda sudah terkirim dan akan kami tindak lanjuti.');
    }
}

