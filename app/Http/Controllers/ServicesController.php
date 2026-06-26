<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    public function index()
    {
        $services = Products::where('type','=','service')->get();
        return view('dashboard.products',[
            'products' => $services,
            'product_type' => 'service'
        ]);
    }
}
