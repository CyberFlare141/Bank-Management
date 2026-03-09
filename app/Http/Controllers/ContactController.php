<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    private const RECIPIENTS = [
        'all' => [
            'sazid.cse.20230104140@aust.edu',
            'samiul.cse.20230104142@aust.edu',
            'masrafi.cse.20230104141@aust.edu',
        ],
        'sazid' => ['sazid.cse.20230104140@aust.edu'],
        'samiul' => ['samiul.cse.20230104142@aust.edu'],
        'masrafi' => ['masrafi.cse.20230104141@aust.edu'],
    ];

    public function create(): View
    {
        return view('contact', [
            'user' => Auth::user(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'recipient' => 'required|in:all,sazid,samiul,masrafi',
            'subject' => 'required|string|max:150',
            'message' => 'required|string|max:3000',
        ]);

        $user = $request->user();
        $recipientGroup = self::RECIPIENTS[$validated['recipient']];
        $payload = [
            'sender_name' => (string) $user->name,
            'sender_email' => (string) $user->email,
            'subject' => (string) $validated['subject'],
            'message_body' => (string) $validated['message'],
            'submitted_at' => now()->format('F j, Y g:i A T'),
            'recipient_scope' => $validated['recipient'],
        ];

        $primaryRecipient = $recipientGroup[0];
        $ccRecipients = array_slice($recipientGroup, 1);

        $mail = Mail::to($primaryRecipient);
        if ($ccRecipients !== []) {
            $mail->cc($ccRecipients);
        }

        $mail->send(new ContactMessageMail($payload));

        return redirect()
            ->route('contact.create')
            ->with('status', 'Your message has been sent successfully. Our team will respond shortly.');
    }
}
