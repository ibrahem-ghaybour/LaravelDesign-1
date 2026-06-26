<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Services\ImageService;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function __construct(protected ImageService $imageService) {}

    public function index()
    {
        $partners = Partner::select(
            columnLocalize('title', table: 'partners') . ' as title',
            'id',
            'img',
        )
            ->where('visible', 1)
            ->get();

        return view('dashboard.partner.partner', compact('partners'));
    }

    public function create()
    {
        return view('dashboard.partner.create');
    }

    /**
     * Store a new partner logo.
     * FIX: image processing moved to ImageService.
     * FIX: use Partner::create() instead of Partner::insert().
     */
    public function store(Request $request)
    {
        $request->validate([
            'title_en' => 'required|max:255',
            'title_ar' => 'required|max:255',
            'image'    => 'nullable|file',
        ]);

        $imgName = '';

        if ($request->hasFile('image')) {
            $imgName = $this->imageService->store($request->file('image')) ?? '';
        }

        Partner::create([
            'title_en' => $request->input('title_en'),
            'title_ar' => $request->input('title_ar'),
            'img'      => $imgName,
        ]);

        return redirect('/dashboard/partner');
    }

    public function edit(Partner $partner)
    {
        return view('dashboard.partner.edit', compact('partner'));
    }

    /**
     * Update an existing partner.
     * FIX: image processing moved to ImageService.
     */
    public function update(Partner $partner, Request $request)
    {
        $request->validate([
            'title_en' => 'required|max:255',
            'title_ar' => 'required|max:255',
            'image'    => 'nullable|file',
        ]);

        $imgName = $partner->img; // default: keep existing image

        if ($request->hasFile('image')) {
            $this->imageService->delete($partner->img);
            $imgName = $this->imageService->store($request->file('image')) ?? $partner->img;
        }

        $partner->update([
            'title_ar' => $request->input('title_ar'),
            'title_en' => $request->input('title_en'),
            'img'      => $imgName,
        ]);

        return redirect()->back()->with('success', 'Partner updated successfully.');
    }

    public function destroy(Partner $partner)
    {
        $this->imageService->delete($partner->img);
        $partner->delete();

        return back();
    }
}
