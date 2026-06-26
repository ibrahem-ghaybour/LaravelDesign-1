<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address;

class AddressController extends Controller
{
    public function __construct(){

        $this->mimeTypeAllowed  = ['image/jpeg','image/png','image/webp','image/svg+xml'];
    }
    public function index()
    {
        $addresses = Address::where('visible','1')
                            
                            ->get();
        return view('dashboard.address.address',[
            'addresses' => $addresses,
            // 'product_type'=>'product'
        ]);
    }

    public function show(Address $address)
    {
        return view('frontend.single',[
            'address' => $address
        ]);
    }

    public function create()
    {
        // dd(public_path());
        return view('dashboard.address.create');
    }

    public function store(Request $request)
    {


        $title = $request->input('title');
        $title_ar = $request->input('title_ar');
        $address = $request->input('address');
        $address_ar = $request->input('address_ar');
        $phone = $request->input('phone');
        $email = $request->input('email');
        $imgName1 = '';

        if($request->hasFile('image')){
           $imgName = $request->file('image');

           $getMimeType = $request->image->getMimeType();

            if(in_array($getMimeType,$this->mimeTypeAllowed)){

               $imgName1 = time() . '.' . $imgName->getClientOriginalExtension();
               $destinationPath = 'images/address/';

               $imgName->move($destinationPath, $imgName1);
           }
        }
       
        $rank = Address::orderBy('rank','desc')->select('rank')->first()->rank;
        Address::insert([
            'title'=>$title,
            'title_ar'=>$title_ar,
            'address'=>$address,
            'address_ar'=>$address_ar,
            'phone'=>$phone,

            'rank'=>$rank+5,
            'email'=>$email,
            'img'=>$imgName1,
        ]);


        return redirect('/dashboard/address');
    }

    public function edit(Address $address)
    {
       
        return view('dashboard.address.edit',[
            'address' => $address,
        ]);
    }

    public function update($address,Request $request)
    {
        $address = Address::findOrFail($address);
        $validated = $request->validate([
            'title_ar'      => 'required|max:255',
            'image'         =>'file',
        ]);
        $imgName1 = '';
        $data = $request->all();
        if ($request->hasFile('image'))
        {
            $image = $request->file('image');

            $getMimeType = $request->image->getMimeType();

            if(in_array($getMimeType,$this->mimeTypeAllowed)){

               $imgName1 = time() . '.' . $image->getClientOriginalExtension();
               $destinationPath = 'images/address/';

               $data['image'] = $imgName1;
               $image->move($destinationPath, $imgName1);
            }else dd($getMimeType);


        }
        if(empty($imgName1))
        {
            $data['image'] = $address->img;
        }
        
        $address = Address::where('id',$address->id)->update([
            'title_ar'      => $data['title_ar'],
            'title'      => $data['title'],
            'img'         =>$data['image'],
            'address'=>$data['address'],
            'address_ar'=>$data['address_ar'],
            'active'=>$data['active'],
            'rank'=>$data['rank'],
            'phone'=>$data['phone'],
            'email'=>$data['email'],
        ]);


        return  redirect()->back()->with('success','the item is successfully updated');

    }

    public function destroy(Address $address)
    {
        $address->delete();
        return back();
    }

}
