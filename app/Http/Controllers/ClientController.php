<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Services\ImageService;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function __construct(protected ImageService $imageService) {}

    public function index()
    {
        $clients = Client::select(
            columnLocalize('title', table: 'clients') . ' as title',
            'id',
            'img',
        )
            ->where('visible', 1)
            ->get();

        return view('dashboard.client.client', compact('clients'));
    }

    public function create()
    {
        return view('dashboard.client.create');
    }

    /**
     * Store a new client/stat card.
     * FIX: image processing moved to ImageService.
     * FIX: use Client::create() instead of Client::insert() so timestamps work.
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

        Client::create([
            'title_en'   => $request->input('title_en'),
            'title_ar'   => $request->input('title_ar'),
            'numberText' => $request->input('numberText'),
            'img'        => $imgName,
        ]);

        return redirect('/dashboard/client');
    }

    public function edit(Client $client)
    {
        return view('dashboard.client.edit', compact('client'));
    }

    /**
     * Update an existing client.
     * FIX: image processing moved to ImageService.
     */
    public function update(Client $client, Request $request)
    {
        $request->validate([
            'title_en' => 'required|max:255',
            'title_ar' => 'required|max:255',
            'image'    => 'nullable|file',
        ]);

        $imgName = $client->img; // default: keep existing image

        if ($request->hasFile('image')) {
            $this->imageService->delete($client->img);
            $imgName = $this->imageService->store($request->file('image')) ?? $client->img;
        }

        $client->update([
            'title_ar'   => $request->input('title_ar'),
            'title_en'   => $request->input('title_en'),
            'img'        => $imgName,
            'numberText' => $request->input('numberText'),
        ]);

        return redirect()->back()->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        $this->imageService->delete($client->img);
        $client->delete();

        return back();
    }
}
