<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use App\Models\Products;
use App\Services\ImageService;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    public function __construct(protected ImageService $imageService)
    {
        // ImageService handles all image processing — no more duplicated code
    }

    public function index()
    {
        $sliders = Slider::select(
            columnLocalize('title',       table: 'slider') . ' as title',
            columnLocalize('description', table: 'slider') . ' as description',
            'id',
            'img',
        )
            ->where('visible', 1)
            ->get();

        return view('dashboard.slider.slider', compact('sliders'));
    }

    public function create()
    {
        return view('dashboard.slider.create');
    }

    /**
     * Store a new slider.
     * FIX: image processing moved to ImageService.
     * FIX: use Slider::create() instead of Slider::insert() so timestamps are set.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title_en'       => 'required|max:255',
            'title_ar'       => 'required|max:255',
            'image'    => 'nullable|image|mimes:jpeg,png,webp,svg|max:4096',
        ]);

        $imgName = '';

        if ($request->hasFile('image')) {
            // Sliders use cover crop (not logo-style contain)
            $imgName = $this->imageService->store($request->file('image')) ?? '';
        }

        // Resolve slugs from the linked product (if any)
        [$slugAr, $slugEn] = $this->resolveSlugs($request->input('product_id'));

        Slider::create([
            'title_en'       => $request->input('title_en'),
            'title_ar'       => $request->input('title_ar'),
            'description_en' => $request->input('description_en'),
            'description_ar' => $request->input('description_ar'),
            'img'            => $imgName,
            'product_id'     => $request->input('product_id', 0),
            'slug_ar'        => $slugAr,
            'slug_en'        => $slugEn,
        ]);

        clearFrontendCache();

        return redirect('/dashboard/slider');
    }

    public function edit(Slider $slider)
    {
        return view('dashboard.slider.edit', compact('slider'));
    }

    /**
     * Update an existing slider.
     * FIX: image processing moved to ImageService.
     */
    public function update(Slider $slider, Request $request)
    {
        $request->validate([
            'image'    => 'nullable|image|mimes:jpeg,png,webp,svg|max:4096',
        ]);

        $imgName = $slider->img; // default: keep existing image

        if ($request->hasFile('image')) {
            // Delete the old image before saving the new one
            $this->imageService->delete($slider->img);
            $imgName = $this->imageService->store($request->file('image')) ?? $slider->img;
        }

        [$slugAr, $slugEn] = $this->resolveSlugs($request->input('product_id'));

        $slider->update([
            'title_ar'       => $request->input('title_ar'),
            'title_en'       => $request->input('title_en'),
            'description_ar' => $request->input('description_ar'),
            'description_en' => $request->input('description_en'),
            'img'            => $imgName,
            'product_id'     => $request->input('product_id', 0),
            'slug_ar'        => $slugAr,
            'slug_en'        => $slugEn,
        ]);

        clearFrontendCache();

        return redirect()->back()->with('success', 'Slider updated successfully.');
    }

    public function destroy(Slider $slider)
    {
        $this->imageService->delete($slider->img);
        $slider->delete();
        clearFrontendCache();

        return back();
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  HELPERS
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Given a product_id, return [slug_ar, slug_en] from the products table.
     * Returns ['', ''] when no product is linked.
     */
    private function resolveSlugs(?int $productId): array
    {
        if (! $productId) {
            return ['', ''];
        }

        $product = Products::select('slug_ar', 'slug_en')
            ->where('id', $productId)
            ->first();

        return $product
            ? [$product->slug_ar, $product->slug_en]
            : ['', ''];
    }
}
