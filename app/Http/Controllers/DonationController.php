<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class DonationController extends Controller
{
    public function index()
    {
        $settings = [];
        try {
            $rows = DB::table('settings')->get();
            foreach ($rows as $row) {
                $key = $row->key ?? $row->name ?? null;
                $val = $row->value ?? $row->content ?? '';
                if ($key) $settings[$key] = $val;
            }
        } catch (\Exception $e) {}

        return view('frontend.donate', compact('settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name'      => 'required|string|max:255',
            'email'          => 'required|email|max:255',
            'amount'         => 'required|numeric|min:1',
            'receipt'        => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'payment_method' => 'required|in:bank,cliq',
            'phone'          => 'nullable|string|max:30',
            'notes'          => 'nullable|string|max:1000',
        ], [
            'full_name.required'      => 'الاسم الكامل مطلوب',
            'email.required'          => 'البريد الإلكتروني مطلوب',
            'email.email'             => 'البريد الإلكتروني غير صحيح',
            'amount.required'         => 'المبلغ مطلوب',
            'amount.min'              => 'يجب أن يكون المبلغ أكبر من صفر',
            'receipt.required'        => 'يرجى رفع إيصال الدفع',
            'receipt.mimes'           => 'يجب أن يكون الملف صورة أو PDF',
            'receipt.max'             => 'حجم الملف يجب أن لا يتجاوز 5MB',
            'payment_method.required' => 'يرجى اختيار طريقة الدفع',
        ]);

        // Store receipt file
        $receiptPath = $request->file('receipt')->store('donations/receipts', 'public');

        $method = $request->payment_method === 'bank' ? 'حساب بنكي' : 'كليك – عُمان';

        // Save to database
        DB::table('donations')->insert([
            'full_name'      => $request->full_name,
            'email'          => $request->email,
            'amount'         => $request->amount,
            'payment_method' => $request->payment_method,
            'phone'          => $request->phone,
            'notes'          => $request->notes,
            'receipt_path'   => $receiptPath,
            'status'         => 'pending',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        // Send confirmation email to donor
        try {
            Mail::send([], [], function ($message) use ($request, $method) {
                $message->to($request->email, $request->full_name)
                    ->subject('شكراً لتبرعك – بيت تراث العرب')
                    ->html("
                        <div dir='rtl' style='font-family:Arial,sans-serif;max-width:600px;margin:auto;padding:24px;'>
                            <h2 style='color:#1a1a6e;'>شكراً لتبرعك، {$request->full_name}!</h2>
                            <p>لقد تلقينا إشعارك وسنؤكد وصول التبرع قريباً.</p>
                            <table style='width:100%;border-collapse:collapse;margin:20px 0;'>
                                <tr style='background:#f5f5ff;'>
                                    <td style='padding:10px 16px;font-weight:bold;border:1px solid #e0e0f0;'>المبلغ</td>
                                    <td style='padding:10px 16px;border:1px solid #e0e0f0;'>{$request->amount} د.أ</td>
                                </tr>
                                <tr>
                                    <td style='padding:10px 16px;font-weight:bold;border:1px solid #e0e0f0;'>طريقة الدفع</td>
                                    <td style='padding:10px 16px;border:1px solid #e0e0f0;'>{$method}</td>
                                </tr>
                                <tr style='background:#f5f5ff;'>
                                    <td style='padding:10px 16px;font-weight:bold;border:1px solid #e0e0f0;'>التاريخ</td>
                                    <td style='padding:10px 16px;border:1px solid #e0e0f0;'>" . now()->format('Y-m-d H:i') . "</td>
                                </tr>
                            </table>
                            <p style='color:#555;'>تبرعك يساهم في نشر الأمل والحفاظ على التراث.</p>
                            <p style='color:#888;font-size:0.9em;'>– فريق بيت تراث العرب</p>
                        </div>
                    ");
            });
        } catch (\Exception $e) {
            // Email failed — don't block the user
        }

        return redirect()->route('donate')->with('success', true);
    }
}
