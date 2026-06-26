<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solutions;

class SolutionsController extends Controller
{
    public function __construct(){

        $this->mimeTypeAllowed  = ['image/jpeg','image/png','image/webp','image/svg+xml'];
    }
    public function index()
    {
        $solutions = Solutions::get();
        return view('dashboard.solution.solution',[
            'solutions' => $solutions,
            // 'product_type'=>'product'
        ]);
    }

    public function show(Solutions $solution)
    {
        return view('frontend.single',[
            'solution' => $solution
        ]);
    }

    public function create()
    {
        // dd(public_path());
        return view('dashboard.solution.create');
    }

    public function store(Request $request)
    {


        $title = $request->input('title');
        $title_ar = $request->input('title_ar');
        $desc  = $request->input('description');
        $desc_ar = $request->input('description_ar');
        $bgName1 = '';
        $imgName = '';

        if($request->hasFile('image')){
           $imgName = $request->file('image');
           $imgName1 = time() . '.' . $imgName->getClientOriginalExtension();
           $destinationPath = 'images/solution/';

           $imgName->move($destinationPath, $imgName1);
        }
        Solutions::insert([
            'title'=>$title,
            'title_ar'=>$title_ar,
            'description'=>$desc,
            'description_ar'=>$desc_ar,
            'img'=>$imgName1,
        ]);


        return redirect('/dashboard/solutions');
    }

    public function edit(Solutions $solution)
    {
        return view('dashboard.solution.edit',[
            'solution' => $solution,
        ]);
    }

    public function update($solution,Request $request)
    {
        $solution = Solutions::findOrFail($solution);
        $validated = $request->validate([
            'title_ar'      => 'required|max:255',
          //  'description_ar'=> 'required',
            'image'         =>'file',
            'bg'         =>'file',
        ]);
        $imgName1 = '';
        $bgName1 = '';
        $data = $request->all();
        if ($request->hasFile('image'))
        {
            $image = $request->file('image');

            $getMimeType = $request->image->getMimeType();

            if(in_array($getMimeType,$this->mimeTypeAllowed)){

               $imgName1 = time() . '.' . $image->getClientOriginalExtension();
               $destinationPath = 'images/solution/';

               $data['image'] = $imgName1;
               $image->move($destinationPath, $imgName1);
            }


        }
        if(empty($imgName1))
        {
            $data['image'] = $solution->img;
        }
        
        

        $solution = Solutions::where('id',$solution->id)->update([
            'title_ar'      => $data['title_ar'],
            'title'      => $data['title'],
            'description_ar'=>$data['description_ar'],
            'description'=>$data['description'],
            'img'         =>$data['image'],
        ]);


        return  redirect()->back()->with('success','the item is successfully updated');

    }

    public function destroy(Solutions $solution)
    {
        $solution->delete();
        return back();
    }

}
