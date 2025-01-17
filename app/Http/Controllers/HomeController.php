<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product;
use App\ProductImage;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $data['categories'] = Category::active()->orderBy('name')->get();
        $data['featured_category_products'] = Category::with(['products'=>function($query){
            return $query->with(['product_images'=>function($query2){
                return $query2->get();
            }])->limit(5)->active();
        }])->active()->first();
        $data['new_arrivals'] = Product::with(['product_images','category'])->orderBy('id','desc')->limit(6)->get();
        $data['featured_products'] = Product::with(['product_images','category'])->where('is_featured',1)->orderBy('id','desc')->limit(6)->get();
//        dd($data);
        return view('front.home',$data);
    }

    public function quick_view($id)
    {
        $data['quick_view'] = Product::findOrFail($id);
        $data['product_images'] = ProductImage::where('product_id',$id)->get();
        return view('front.quick_view',$data);
    }

    public function product_details($id)
    {
        $product = Product::findOrFail($id);
        $data['product_details'] = $product;
        $data['related_products'] = Product::where('category_id',$product->category_id)->orderBy('id','desc')->limit(6)->get();
        $data['product_images'] = ProductImage::where('product_id',$id)->get();
        $data['lest_products'] = Product::with(['product_images' => function($query){
            return $query->get();
        }])->active()->orderBy('id','desc')->limit(4)->get();

//        dd($data);
        return view('front.product_details',$data);
    }
}
