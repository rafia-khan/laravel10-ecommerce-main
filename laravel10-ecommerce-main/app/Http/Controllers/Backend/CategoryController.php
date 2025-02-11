<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with('category:id,cat_ust,name')->get();
        return view('backend.pages.category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('cat_ust',null)->get();
        return view('backend.pages.category.edit', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        if ($request->hasFile('image')) {
            $img = $request->file('image');
            $folderName = $request->name;
            $uploadFolder = 'img/category/';
            folderOpen($uploadFolder);
            $imgurl = resimyukle($img, $folderName, $uploadFolder);
        }

        Category::create([
            'name' => $request->name,
            'cat_ust' => $request->cat_ust,
            'status' => $request->status,
            'content' => $request->content,
            'image' => $imgurl ?? NULL,
        ]);

        return back()->withSuccess('Category created successfully');
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
        $category = Category::where('id', $id)->first();
        $categories = Category::get();
        return view('backend.pages.category.edit', compact('category', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::where('id', $id)->firstOrFail();

        if ($request->hasFile('image')) {
            dosyasil($category->image);

            $img = $request->file('image');
            $folderName = $request->name;
            $uploadFolder = 'img/category/';
            folderOpen($uploadFolder);
            $imgurl = resimyukle($img, $folderName, $uploadFolder);
        }

        $category->update([
            'name' => $request->name,
            'cat_ust' => $request->cat_ust,
            'status' => $request->status,
            'content' => $request->content,
            'image' => $imgurl ?? $category->image,
        ]);

        return back()->withSuccess('Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = Category::where('id', $id)->firstOrFail();
        dosyasil($category->image);
        $category->delete();
        return back()->with(['message'=>'Category deleted successfully']);
    }

    public function status(Request $request){
        $update = $request->statu;
        $updateCheck = $update == "false" ? '0' : '1';
        Category::where('id', $request->id)->update(['status' => $updateCheck]);
        return response(['error'=>false, 'status'=>$update]);
    }
}
