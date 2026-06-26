<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DonationDashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('donations')->orderBy('id', 'desc');

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }
        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $query->where(function($q) use ($s) {
                $q->where('full_name', 'like', $s)
                  ->orWhere('email', 'like', $s)
                  ->orWhere('phone', 'like', $s);
            });
        }

        $donations = $query->paginate(20);

        // Stats
        $stats = [
            'total'        => DB::table('donations')->count(),
            'pending'      => DB::table('donations')->where('status', 'pending')->count(),
            'confirmed'    => DB::table('donations')->where('status', 'confirmed')->count(),
            'total_amount' => DB::table('donations')->where('status', 'confirmed')->sum('amount'),
        ];

        return view('dashboard.donations', compact('donations', 'stats'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,rejected',
        ]);

        DB::table('donations')->where('id', $id)->update([
            'status'     => $request->status,
            'updated_at' => now(),
        ]);

        $messages = [
            'confirmed' => 'تم تأكيد التبرع بنجاح ✓',
            'rejected'  => 'تم رفض التبرع',
            'pending'   => 'تم إعادة التبرع لحالة الانتظار',
        ];

        return back()->with('success', $messages[$request->status]);
    }

    public function destroy($id)
    {
        $donation = DB::table('donations')->where('id', $id)->first();

        if ($donation && $donation->receipt_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($donation->receipt_path);
        }

        DB::table('donations')->where('id', $id)->delete();

        return back()->with('success', 'تم حذف التبرع بنجاح');
    }

    public function export()
    {
        $donations = DB::table('donations')->orderBy('id', 'desc')->get();

        $csvData = "ID,الاسم,البريد,الهاتف,المبلغ,طريقة الدفع,الحالة,التاريخ\n";
        foreach ($donations as $d) {
            $method = $d->payment_method === 'bank' ? 'حساب بنكي' : 'كليك';
            $status = match($d->status) {
                'confirmed' => 'مؤكدة',
                'rejected'  => 'مرفوضة',
                default     => 'بانتظار التأكيد',
            };
            $csvData .= implode(',', [
                $d->id,
                '"' . $d->full_name . '"',
                $d->email,
                $d->phone ?? '',
                $d->amount,
                $method,
                $status,
                $d->created_at,
            ]) . "\n";
        }

        return response($csvData, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="donations_' . now()->format('Y-m-d') . '.csv"',
        ]);
    }
}
