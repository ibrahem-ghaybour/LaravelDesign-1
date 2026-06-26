<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{

    public function about()
    {
        return view('dashboard.aboutForm');
    }
    public function aboutpagestore(Request $request)
    {
        $validated = $request->validate([
            // 'title'         => 'required|max:255',
            'title_ar'      => 'required|max:255',
            // 'description'   => 'required',
            'description_ar'=> 'required',
            'slug'          =>'required|unique:products|alpha_dash',
            'image'         => 'mimes:jpeg,jpg,png,gif|required|max:5000',
            'type'          =>'max:255'
        ]);

        $data = $request->all();
        if($request->image)
        {
            $data['image'] =  $request->file('image')->store('images','images');
        }
        Products::create($data);
        return redirect()->back();

    }
}
