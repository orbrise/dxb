<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::all();
        return view('admin.countries.countries', compact('countries'));
    }

    public function addCountry(Request $req){

        $req->validate([
        'countryname' => 'required|unique:countries,nicename',
        ]);

        $city = new Country;
        $city->nicename = $req->countryname;
        $city->phonecode = $req->code;

        if($city->save()){
            return back()->with("success", "City added successfully");
        }
    }


    public function updateCountry(Request $req){

        $city = Country::findOrFail($req->rid);
        $city->nicename = $req->name;
        $city->phonecode = $req->code;

        if($city->save()){
            return "success";
        }
    }

    public function delCountry(Request $req)
    {
        if(Country::where('id',$req->rid)->delete()){
            return 'success';
        }
    }
}
