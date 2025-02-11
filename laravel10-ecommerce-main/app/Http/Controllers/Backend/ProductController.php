<?php

namespace App\Http\Controllers\Backend;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('category:id,cat_ust,name')->orderBy('id','desc')->paginate(20);
        return view('backend.pages.product.index',compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::get();
        return view('backend.pages.product.create',compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        if ($request->hasFile('image')) {
            $img = $request->file('image');
            $folderName = $request->name;
            $uploadFolder = 'img/product/';
            folderOpen($uploadFolder);
            $imgurl = resimyukle($img, $folderName, $uploadFolder);
        }

        $product = [
            'name'=>$request->name,
            'category_id'=>$request->category_id,
            'content'=>$request->content,
            'short_text'=>$request->short_text,
            'price'=>$request->price,
            'size'=>$request->size,
            'color'=>$request->color,
            'qty'=>$request->qty,
             // 'kdv'=>$request->kdv,
             // 'title'=>$request->title,
             // 'description'=>$request->description,
             // 'keywords'=>$request->keywords,
            'status'=>$request->status,
            'image' => $imgurl ?? NULL,
        ];
        // dd($product);
    $data = Product::create($product);
// dd($data);
        return redirect()->route('panel.product.index')->with('Product successfully created!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::where('id',$id)->first();
        $categories = Category::get();
        return view('backend.pages.product.edit',compact('product','categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, string $id)
    {
        $product = Product::where('id',$id)->firstOrFail();

        if ($request->hasFile('image')) {
            dosyasil($product->image);

            $img = $request->file('image');
            $folderName = $request->name;
            $uploadFolder = 'img/product/';
            folderOpen($uploadFolder);
            $imgurl = resimyukle($img, $folderName, $uploadFolder);
        }

        $product->update([
            'name'=>$request->name,
            'category_id'=>$request->category_id,
            'content'=>$request->content,
            'short_text'=>$request->short_text,
            'price'=>$request->price,
            'size'=>$request->size,
            'color'=>$request->color,
            'qty'=>$request->qty,
            // 'kdv'=>$request->kdv,
            // 'title'=>$request->title,
            // 'description'=>$request->description,
            // 'keywords'=>$request->keywords,
            'status'=>$request->status,
            'image'=> $imgurl ?? $product->image
        ]);

        return redirect()->route('panel.product.index')->withSuccess('Product Updated successfully!');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {

        $product = Product::where('id',$request->id)->firstOrFail();

        dosyasil($product->image);

        $product->delete();
        return back()->with(['message'=>'Product deleted successfully.']);
    }

    public function status(Request $request) {

        $update = $request->statu;
        $updateCheck = $update == "false" ? '0' : '1';

        Product::where('id',$request->id)->update(['status'=> $updateCheck]);
        return response(['error'=>false,'status'=>$update]);
    }
}
