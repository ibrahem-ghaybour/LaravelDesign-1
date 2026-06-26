<?php

use App\Http\Controllers\FrontHome;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\SolutionsController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\QAController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\DonationDashboardController;
use App\Models\ProductImage;


Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::get('/', [FrontHome::class, 'services'])->name('main');

// ── Static pages ─────────────────────────────────────────────────────────────
Route::get('/free-trail', fn () => view('frontend.freetrail'))->name('freetrial');
Route::get('/about-us',   fn () => view('frontend.about'))->name('about');
Route::view('/undercontstruction', 'frontend.underconstruction')->name('underconstruction');

// ── Redirects for old URLs (SEO: keeps external links alive) ─────────────────
Route::redirect('/Privacy-policy', '/privacy', 301);
Route::redirect('/refund-policy',  '/refundpolicy', 301);
Route::get('/privacy',      [FrontHome::class, 'pageBySlug'])->defaults('slug', 'privacy');
Route::get('/refundpolicy', [FrontHome::class, 'pageBySlug'])->defaults('slug', 'refund');

// ── Dynamic pages ─────────────────────────────────────────────────────────────
Route::get('/pages/{id}', [FrontHome::class, 'page'])->name('pages');

// ── Q&A ───────────────────────────────────────────────────────────────────────
Route::get('/indexQA', [QAController::class, 'index'])->name('indexQA');

// ── Products / News / Solutions / Services ───────────────────────────────────
Route::get('/product',    [FrontHome::class, 'Product']  )->name('product');
Route::get('/news',       [FrontHome::class, 'news']     )->name('news');
Route::get('/solutions',  [FrontHome::class, 'solutions'])->name('solutions');
Route::get('/services',   [FrontHome::class, 'service']  )->name('services');

// ── Articles, Clients, Partners ──────────────────────────────────────────────
Route::view('/articles', 'frontend.articles')->name('frontend.articles');
Route::get('/clients',   [FrontHome::class, 'clients'] )->name('frontend.clients');
Route::get('/partners',  [FrontHome::class, 'partners'])->name('frontend.partners');

// ── Contact ───────────────────────────────────────────────────────────────────
Route::get ('/contact-us', [ContactController::class, 'index'] )->name('contact');
Route::post('/contact-us', [ContactController::class, 'create'])->name('contact1');

// ── Single product (by slug) ──────────────────────────────────────────────────
Route::get('/product/{slug}', [FrontHome::class, 'show'])->name('single.product');

// ── Category grid ─────────────────────────────────────────────────────────────
Route::get('/category/{category}', [FrontHome::class, 'show1'])->name('category');

Route::get('/donate', [DonationController::class, 'index'])->name('donate');
Route::post('/donate', [DonationController::class, 'store'])->name('donate.store');


Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
Route::post('/settings/theme', [SettingsController::class, 'updateTheme'])->name('settings.theme');
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');
