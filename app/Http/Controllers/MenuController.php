<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function index()
    {
        // FIX: cache was being cleared on every page load of the menu list.
        //      Removed clearFrontendCache() from here — it should only run on write operations.
        $menuItems = MenuItem::select(
            columnLocalize('title', table: 'menu_items') . ' as title',
            'id',
        )
            ->orderBy('rank')
            ->get();

        return view('dashboard.menuItem.menuItem', compact('menuItems'));
    }

    public function create()
    {
        $parents = MenuItem::select(
            columnLocalize('title', table: 'menu_items') . ' as title',
            'id',
        )->get();

        return view('dashboard.menuItem.create', compact('parents'));
    }

    /**
     * Store a new menu item.
     * FIX: use MenuItem::create() instead of MenuItem::insert() so timestamps work.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title_en' => 'required|max:255',
            'title_ar' => 'required|max:255',
            'link'     => 'nullable|max:255',
        ]);

        // Auto-rank: place new item after the last existing rank
        $maxRank = MenuItem::orderByDesc('rank')->value('rank') ?? 0;

        // Build slugs from titles
        $slugAr = preg_replace('/[^\p{Arabic}0-9\s\-]/u', '', $request->input('title_ar'));
        $slugAr = preg_replace('/\s+/u', '-', trim($slugAr));
        $slugEn = Str::slug($request->input('title_en'));

        MenuItem::create([
            'title_en'  => $request->input('title_en'),
            'title_ar'  => $request->input('title_ar'),
            'parent_id' => $request->input('parent_id'),
            'link'      => $request->input('link', '/'),
            'rank'      => $maxRank + 5,
            'slug_ar'   => $slugAr,
            'slug_en'   => $slugEn,
        ]);

        clearFrontendCache();

        return redirect('/dashboard/menuItem');
    }

    public function edit(MenuItem $menuItem)
    {
        $parents = MenuItem::select(
            columnLocalize('title', table: 'menu_items') . ' as title',
            'id',
        )->get();

        return view('dashboard.menuItem.edit', compact('menuItem', 'parents'));
    }

    /**
     * Update a menu item.
     * FIX: clearFrontendCache() was placed AFTER the return statement (dead code).
     *      Moved it before the redirect.
     */
    public function update(MenuItem $menuItem, Request $request)
    {
        $request->validate([
            'title_ar' => 'required|max:255',
            'title_en' => 'required|max:255',
        ]);

        $slugAr = preg_replace('/[^\p{Arabic}0-9\s\-]/u', '', $request->input('title_ar'));
        $slugAr = preg_replace('/\s+/u', '-', trim($slugAr));
        $slugEn = Str::slug($request->input('title_en'));

        $menuItem->update([
            'title_ar'  => $request->input('title_ar'),
            'title_en'  => $request->input('title_en'),
            'parent_id' => $request->input('parent_id'),
            'link'      => $request->input('link'),
            'rank'      => $request->input('rank'),
            'slug_ar'   => $slugAr,
            'slug_en'   => $slugEn,
        ]);

        // FIX: this line was previously AFTER "return redirect()->back()" — dead code
        clearFrontendCache();

        return redirect()->back()->with('success', 'Menu item updated successfully.');
    }

    public function destroy(MenuItem $menuItem)
    {
        $menuItem->delete();
        clearFrontendCache();

        return back();
    }
}
