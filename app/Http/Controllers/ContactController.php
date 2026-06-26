<?php

namespace App\Http\Controllers;

use App\Models\Contacts;
use App\Models\Setting;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ContactController extends Controller
{
    /**
     * Show the contact page.
     * FIX: replaced 4 separate Setting::where('id',...)->first() queries
     *      with a single whereIn() call, then keyed by ID.
     */
    public function index()
    {
        $addresses = Address::orderBy('rank')->get();

        $locale  = app()->getLocale();
        $descCol = 'description_' . $locale;

        // Single query instead of 4 separate queries
        $settings = Setting::whereIn('id', [22, 23, 24, 25])
            ->get()
            ->keyBy('id');

        $data = [
            'contactUsTxt1' => optional($settings->get(22))->{$descCol} ?? '',
            'contactUsTxt2' => optional($settings->get(23))->{$descCol} ?? '',
            'contactUsTxt3' => optional($settings->get(24))->{$descCol} ?? '',
            'contactUsTxt4' => optional($settings->get(25))->{$descCol} ?? '',
        ];

        return view('frontend.contact', compact('addresses', 'data'));
    }

    /**
     * Handle the contact form submission.
     * Validates, verifies reCAPTCHA, then stores and redirects back.
     */
    public function create(Request $request)
    {
        $request->validate([
            'name'                 => ['required', 'string', 'min:3', 'max:255'],
            'email'                => ['required', 'email'],
            'phone'                => ['required', 'numeric'],
            'message'              => ['nullable', 'max:1000'],
            'g-recaptcha-response' => ['required'],
        ]);

        // Verify reCAPTCHA token with Google
        $captchaResponse = Http::asForm()->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'secret'   => config('services.recaptcha.secret_key'),
                'response' => $request->input('g-recaptcha-response'),
                'remoteip' => $request->ip(),
            ]
        );

        if (! data_get($captchaResponse->json(), 'success')) {
            return back()
                ->withErrors(['g-recaptcha-response' => __('Captcha verification failed')])
                ->withInput();
        }

        // Store the contact — exclude the reCAPTCHA field from the record
        Contacts::create($request->except('g-recaptcha-response'));

        return back()->with('success', __('Message sent successfully!'));
    }

    /** Dashboard: list all contact submissions */
    public function contacts()
    {
        return view('dashboard.contacts');
    }

    /**
     * Dashboard: view a single contact submission.
     * FIX: was ->findOrFail($id)->first() which is wrong.
     *      findOrFail() already returns a single model — ->first() on a Model
     *      would throw a "call to undefined method" error.
     */
    public function singleContact(int $id)
    {
        $contact = Contacts::findOrFail($id);

        return view('dashboard.singleContact', compact('contact'));
    }
}
