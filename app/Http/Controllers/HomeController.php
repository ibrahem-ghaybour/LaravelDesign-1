<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('home', [
            'stats' => config('site.stats'),
            'programs' => config('site.programs'),
            'projects' => config('site.projects'),
            'news' => config('site.news'),
            'partners' => config('site.partners'),
            'heroSlides' => config('site.hero_slides'),
            'impactSteps' => config('site.impact_steps'),
        ]);
    }
}
