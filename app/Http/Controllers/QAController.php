<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;
class QAController extends Controller
{

    public function index()
    {
        $QA = Products::where('type','QA')->get();
        return view('frontend.qa', compact('QA'));
    }

}
