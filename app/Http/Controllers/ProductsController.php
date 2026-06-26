<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\ProductImage;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;




class ProductsController extends Controller
{
    public function __construct(protected ImageService $imageService)
    {
        // ImageService is injected via constructor — no more duplicated image logic
    }
   public function globTe(Request $request)
{
    $manager = new ImageManager(new Driver());

    $files = glob(public_path('images/images/*.webp'));

    foreach ($files as $file) {
        try {
            $image = $manager->read($file);

            // غير الاسم
            $newPath = preg_replace('/\.webp$/i', '_share.jpg', $file);

            $image
                ->cover(1200, 630)
                ->toJpeg(90)
                ->save($newPath);

        } catch (\Throwable $e) {
            \Log::error('Image convert failed', [
                'file' => $file,
                'error' => $e->getMessage(),
            ]);
        }
    }

    return 'done';
}

public function convertToWebP(Request $request)
{
    // ── إعدادات ───────────────────────────────────────────────────
    $IMAGE_DIRS = [
        'images/images/',
        'images/thumb_100/',
        'images/thumb_400/',
    ];
 
    $TABLES = [
        ['products', 'image', 'id'],
        ['sliders',  'image', 'id'],
        ['clients',  'image', 'id'],
        ['partners', 'image', 'id'],
        ['articles', 'image', 'id'],
    ];
 
    $QUALITY         = 82;
    $DELETE_ORIGINAL = false;
    $DRY_RUN         = $request->get('dry') === '1';
    $publicPath      = rtrim(public_path(), '/');
 
    // ── التحقق من GD ──────────────────────────────────────────────
    if (!function_exists('imagewebp')) {
        return back()->with('error', 'GD لا يدعم WebP. شغّل: sudo apt install php-gd');
    }
 
    // ── المعالجة ──────────────────────────────────────────────────
    $stats = ['converted' => 0, 'skipped' => 0, 'missing' => 0, 'failed' => 0, 'db_updated' => 0];
    $log   = [];
 
    foreach ($TABLES as [$table, $col, $idCol]) {
        try {
            $rows = \DB::table($table)
                ->whereNotNull($col)
                ->where($col, '!=', '')
                ->get([$idCol, $col]);
        } catch (\Exception $e) {
            $log[] = ['type' => 'warn', 'msg' => "جدول '$table' غير موجود — تخطي"];
            continue;
        }
 
        $log[] = ['type' => 'table', 'msg' => "جدول: $table — {$rows->count()} سجل"];
 
        foreach ($rows as $row) {
            $originalName = trim($row->$col);
 
            if (empty($originalName) || strtolower($originalName) === 'null') continue;
 
            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
 
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                $stats['skipped']++;
                continue;
            }
 
            $newName   = preg_replace('/\.(jpe?g|png|gif)$/i', '.webp', $originalName);
            $converted = false;
 
            foreach ($IMAGE_DIRS as $dir) {
                $srcPath = $publicPath . '/' . $dir . $originalName;
 
                if (!file_exists($srcPath)) continue;
 
                if ($DRY_RUN) {
                    $log[]     = ['type' => 'dry', 'msg' => "[DRY] $dir$originalName → $newName"];
                    $converted = true;
                    $stats['converted']++;
                } elseif ($this->_convertImage($srcPath, $QUALITY)) {
                    $converted = true;
                    $stats['converted']++;
                    $log[]     = ['type' => 'ok', 'msg' => "$dir$originalName → $newName"];
                    if ($DELETE_ORIGINAL) @unlink($srcPath);
                } else {
                    $stats['failed']++;
                    $log[] = ['type' => 'error', 'msg' => "فشل: $dir$originalName"];
                }
            }
 
            if ($converted && !$DRY_RUN) {
                \DB::table($table)->where($idCol, $row->$idCol)->update([$col => $newName]);
                $stats['db_updated']++;
            } elseif (!$converted) {
                $stats['missing']++;
                $log[] = ['type' => 'warn', 'msg' => "غير موجود: $originalName"];
            }
        }
    }
 
    return view('dashboard.convert_webp', compact('stats', 'log', 'DRY_RUN'));
}
 
// ── Helper خاص بالتحويل ───────────────────────────────────────────
private function _convertImage(string $src, int $quality): bool
{
    $ext  = strtolower(pathinfo($src, PATHINFO_EXTENSION));
    $dest = preg_replace('/\.(jpe?g|png|gif)$/i', '.webp', $src);
 
    if ($ext === 'webp')    return true;
    if (!file_exists($src)) return false;
    if (file_exists($dest)) return true;
 
    $img = match($ext) {
        'jpg', 'jpeg' => @imagecreatefromjpeg($src),
        'png'         => @imagecreatefrompng($src),
        'gif'         => @imagecreatefromgif($src),
        default       => false,
    };
 
    if (!$img) return false;
 
    if (in_array($ext, ['png', 'gif'])) {
        imagepalettetotruecolor($img);
        imagealphablending($img, true);
        imagesavealpha($img, true);
    }
 
    $result = imagewebp($img, $dest, $quality);
    imagedestroy($img);
    return $result;
}
    // ──────────────────────────────────────────────────────────────────────────
    //  DASHBOARD
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * List products in the dashboard.
     * The route name determines which product TYPE to show.
     *
     * FIX: Previously used a chain of ->where('type','<>','...') conditions.
     *      Now uses a clean whereNotIn() call. Also separated privacy/refund
     *      into a private helper to reduce if/else nesting.
     */
    public function index()
    {
        $routeName = Route::current()->getName();

        $products = match ($routeName) {
            'privacy.show' => $this->queryByType('Privacy-policy'),
            'refund.show'  => $this->queryByType('refund policy'),
            default        => $this->queryAllProducts(),
        };

        return view('dashboard.products', compact('products'));
    }

    /** Return localized product rows filtered to a single type */
    private function queryByType(string $type)
    {
        return Products::select(...$this->localizedColumns())
            ->where('type', $type)
            ->get();
    }

    /** Return all product rows except internal/system types */
    private function queryAllProducts()
    {
        // These types are used internally and should not appear in the general list
        $excludedTypes = [
            'service', 'paragraph', 'paragraph1', 'article',
            'solutions', 'service1', 'Privacy-policy', 'refund policy', 'QA',
        ];

        return Products::select(...$this->localizedColumns())
            ->whereNotIn('type', $excludedTypes)
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Show a single product on the frontend.
     * Searches by slug_ar OR slug_en so both languages resolve correctly.
     */


    public function create()
    {
        return view('dashboard.productCreate');
    }

    /**
     * Store a new product.
     * FIX: image processing extracted to ImageService — no more 40-line block here.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title_en'       => 'required|max:255',
            'title_ar'       => 'required|max:255',
            'description_en' => 'required',
            'description_ar' => 'required',
            'image'          => 'nullable|file|mimes:jpeg,jpg,png,gif,webp|max:5000',
            'extra_images.*' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp|max:5000',
            'type'           => 'nullable|max:255',
            'link' => 'nullable|url'
        ]);

        $data = $request->all();

        // Process uploaded image if present
        if ($request->hasFile('image')) {
            $fileName = $this->imageService->store($request->file('image'), $data['type'] ?? '');
            if ($fileName) {
                $data['image'] = $fileName;
            }
        }

        // Generate slugs from titles
        $data['slug_ar'] = $this->makeArabicSlug($request->title_ar);
        $data['slug_en'] = Str::slug($request->title_en);

        $product = Products::create($data);

        // Store extra images
        if ($request->hasFile('extra_images')) {
            foreach ($request->file('extra_images') as $index => $file) {
                $fileName = $this->imageService->store($file, $data['type'] ?? '');
                if ($fileName) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image'      => $fileName,
                        'sort_order' => $index,
                    ]);
                }
            }
        }

        clearFrontendCache();
        
        return redirect()->back()->with('success', 'Item stored successfully.');
    }

    public function fixedAllSlug(){
        
        $rows = Products::get();
        foreach($rows as $product){
            $product->update([
            
            'slug_ar'        => $this->makeArabicSlug($product->title_ar) ,
            'slug_en'        => Str::slug($product->title_en) ,
            
        ]);
        }
       
    }
    public function edit(Products $product)
    {
        return view('dashboard.editProduct', compact('product'));
    }

    /**
     * Update an existing product.
     * FIX: image processing extracted to ImageService.
     * FIX: was using Products::findOrFail() manually — now uses route-model binding.
     */
    public function update(Products $product, Request $request)
    {
        $request->validate([
            'title_en'       => 'required|max:255',
            'title_ar'       => 'required|max:255',
            'description_en' => 'required',
            'description_ar' => 'required',
            'image'          => 'nullable|file|mimes:jpeg,jpg,png,gif,webp|max:5000',
            'extra_images.*' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp|max:5000',
            'type'           => 'nullable|max:255',
            'link'           => 'nullable|url',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            // Delete old images before storing new ones
            $this->imageService->delete($product->image);

            $fileName = $this->imageService->store($request->file('image'), $data['type'] ?? '');
            if ($fileName) {
                $data['image'] = $fileName;
            }
        } else {
            // Keep the existing image filename
            $data['image'] = $product->image;
        }

        // $data['slug_ar'] = $this->makeArabicSlug($data['title_ar']);
        // $data['slug_en'] = Str::slug($data['title_en']);

        $product->update([
            'title_ar'       => $data['title_ar']       ?? null,
            'title_en'       => $data['title_en']       ?? null,
            'excerpt_ar'     => $data['excerpt_ar']     ?? null,
            'excerpt_en'     => $data['excerpt_en']     ?? null,
            'description_ar' => $data['description_ar'] ?? null,
            'description_en' => $data['description_en'] ?? null,
            //'slug_ar'        => $data['slug_ar']        ?? null,
            //'slug_en'        => $data['slug_en']        ?? null,
            'image'          => $data['image'],
            'type'           => $data['type']           ?? null,
            'link'           => $data['link']           ?? null,
        ]);

        // Delete extra images marked for removal
        if ($request->filled('delete_image_ids')) {
            $idsToDelete = explode(',', $request->input('delete_image_ids'));
            $imagesToDelete = ProductImage::whereIn('id', $idsToDelete)
                ->where('product_id', $product->id)
                ->get();
            foreach ($imagesToDelete as $img) {
                $this->imageService->delete($img->image);
                $img->delete();
            }
        }

        // Store new extra images
        if ($request->hasFile('extra_images')) {
            $lastOrder = $product->images()->max('sort_order') ?? -1;
            foreach ($request->file('extra_images') as $index => $file) {
                $fileName = $this->imageService->store($file, $data['type'] ?? '');
                if ($fileName) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image'      => $fileName,
                        'sort_order' => $lastOrder + $index + 1,
                    ]);
                }
            }
        }

        clearFrontendCache();

        return redirect()->back()->with('success', 'Item updated successfully.');
    }

    /**
     * Delete a single extra image via AJAX
     */
    public function deleteImage(ProductImage $productImage)
    {
        $this->imageService->delete($productImage->image);
        $productImage->delete();

        return response()->json(['success' => true]);
    }

    public function destroy(Products $product)
    {
        // Delete all image variants before removing the record
        $this->imageService->delete($product->image);

        // Delete all extra images
        foreach ($product->images as $img) {
            $this->imageService->delete($img->image);
        }
        $product->images()->delete();

        $product->delete();
        clearFrontendCache();

        return back();
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  HELPERS
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Returns the localized SELECT columns array used in most queries.
     * Avoids repeating the same 6-column list across index/show/etc.
     */
    private function localizedColumns(): array
    {
        return [
            columnLocalize('title',       table: 'products') . ' as title',
            columnLocalize('excerpt',     table: 'products') . ' as excerpt',
            columnLocalize('slug',        table: 'products') . ' as slug',
            columnLocalize('description', table: 'products') . ' as description',
            'image',
            'type',
            'link',
            'id',
        ];
    }

    /**
     * Build a URL-safe Arabic slug from an Arabic title.
     * Strips non-Arabic characters, then replaces spaces with hyphens.
     */
    private function makeArabicSlug(string $title): string
    {
        $slug = preg_replace('/[^\p{Arabic}A-Za-z0-9\s\-]/u', '', $title);
        return preg_replace('/\s+/u', '-', trim($slug));
    }
}