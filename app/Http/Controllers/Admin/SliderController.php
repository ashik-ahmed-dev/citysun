<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class SliderController extends Controller
{
    public function index(){
        return view('admin.slider.index');
    }

    public function update(Request $request){
        $request->validate([
            'title' => 'required',
            'subtitle' => 'required',
        ]);
        if ($request->hasFile('image')){
            $request->file('image')->storeAs('', 'slider.png', 'public');
            return redirect()->back()->with('success','data updated successfully');
        }

        $data = array(
            "title" => $request->title,
            "subtitle" => $request->subtitle,
        );
        Settings::updateOrCreate(['key' => 'slider'], ['value' => json_encode($data)]);

        return redirect()->back()->with('success','data saved successfully');
    }
}
