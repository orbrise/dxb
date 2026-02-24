<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Currency;

class CurrencyController extends Controller
{
     public function index()
    {
        $currencies = Currency::all();
        return view('admin.currencies.currencies', compact('currencies'));
    }

    public function addCurrency(Request $req){

        $req->validate([
        'name' => 'required|unique:currencies,currency',
        ]);

        $city = new Currency;
        $city->currency = $req->name;
        $city->code = $req->code;
        $city->symbol = $req->symbol;

        if($city->save()){
            return back()->with("success", "City added successfully");
        }
    }


    public function updateCurrency(Request $req){

        $city = Currency::findOrFail($req->rid);
        $city->currency = $req->name;
        $city->code = $req->code;
        $city->symbol = $req->symbol;
        if($city->save()){
            return "success";
        }
    }

    public function delCurrency(Request $req)
    {
        if(Currency::where('id',$req->rid)->delete()){
            return 'success';
        }
    }
}
