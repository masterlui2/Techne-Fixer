<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function send(Request $request): JsonResponse
    {
        // ── Validate ──────────────────────────────────────────────────────────
        $validator = Validator::make($request->all(), [
            'name'            => ['required', 'string', 'max:255'],
            'email'           => ['required', 'email', 'max:255'],
            'phone'           => ['nullable', 'string', 'max:30'],
            'service'         => ['required', 'string', 'in:web-development,mobile-development,ui-ux-design,cloud-solutions,maintenance,devops'],
            'message'         => ['required', 'string', 'min:10', 'max:5000'],
            'recaptcha_token' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // ── Verify reCAPTCHA ──────────────────────────────────────────────────
        $recaptcha = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => config('services.recaptcha.secret'),
            'response' => $request->input('recaptcha_token'),
            'remoteip' => $request->ip(),
        ]);

        if (! $recaptcha->json('success')) {
            return response()->json([
                'message' => 'reCAPTCHA verification failed. Please try again.',
            ], 422);
        }

        // ── Send Email ────────────────────────────────────────────────────────
        $data = $validator->validated();

        Mail::send('emails.contact', ['formData' => $data], function ($mail) use ($data) {
            $mail->to(config('mail.to.address', env('MAIL_TO_ADDRESS')))
                 ->replyTo($data['email'], $data['name'])
                 ->subject('New Contact Form Submission — ' . ucwords(str_replace('-', ' ', $data['service'])));
        });

        return response()->json([
            'message' => 'Your message has been sent successfully!',
        ], 200);
    }
}