<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\Slider;
use App\Models\Contacts;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $comtactTody = Contacts::whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->count();
        $Contacts = Contacts::
                              //->select('contacts.*',columnLocalize("name", table: "countries")." as name")
                               orderBy('id', 'desc')
                              ->paginate(20);
        return view('dashboard.home',compact('comtactTody','Contacts'));
    }

    public function slider()
    {
        return view('dashboard.slider');
    }

    public function articles(){
        $products = Products::where('type','article')->get();
        return view('dashboard.articles',compact('products'));
    }
    public function paragraphs()
    {
        $products = Products::whereIn('type',['paragraph','paragraph1'])->get();
        return view('dashboard.articles',compact('products'));
    }

    public function paragraphsEdit($id)
    {
        $parag = Products::findOrFail($id);
        return view('dashboard.paragraphsedit',[
            'product' => $parag,
        ]);

    }

    public function paragraphsUpdate(Request $request,$id)
    {
        $parag = Products::findOrFail($id);
        $validated = $request->validate([
            'title_ar'      => 'required|max:255',
            'description_ar'=> 'required',
        ]);
        $data = $request->all();
        $parag = Products::where('id',$parag->id)->update([
            'title_ar'      => $data['title_ar'],
            'title'      => $data['title'],
            'description_ar'=>$data['description_ar'],
            'description'=>$data['description'],
            'type' => 'paragraph'
        ]);
        return  redirect()->back();
    }
}
