<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

use Inertia\Inertia;
use App\Models\Category;

class CategoriesController extends Controller
{

    public function dt_categories(){
        $categories = Category::orderBy('id',"DESC")->get();
        return Inertia::render('Categories', [
            'categories' => $categories
        ]);
    }
    public function destroy(Request $request): RedirectResponse {
        $request->validate([
            'category_id' => 'required'
        ]);
        $category = $request->category_id;
        $resp = Category::where('id',$category)->delete();
        
        if($resp){
            return Redirect::route('categories');
        }else{
            return Redirect::route('categories');
        }
    }
    public function store(Request $request) : RedirectResponse {
        $request->validate([
            'title' => 'required',
            'status' => 'required'
        ]);
        if(!empty($request->category_id)){
            $category = Category::where('id',$request->category_id)->first();
        }else{
            $category = new Category;
        }
        $category->title = $request->title;
        $category->status = $request->status;
        $category->save();
        return Redirect::route('categories');
    }
}
