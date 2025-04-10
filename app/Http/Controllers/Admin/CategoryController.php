<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type', 'service');
        $categories = Category::where('type', $type)->latest()->paginate(10);
        
        return view('admin.categories.index', compact('categories', 'type'));
    }
    
    public function create(Request $request)
    {
        $type = $request->input('type', 'service');
        return view('admin.categories.create', compact('type'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:service,product,news',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);
        
        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'type' => $request->type,
            'description' => $request->description,
            'status' => $request->status,
        ]);
        
        return redirect()->route('admin.categories.index', ['type' => $request->type])
            ->with('success', 'Danh mục đã được tạo thành công.');
    }
    
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }
    
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);
        
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'status' => $request->status,
        ]);
        
        return redirect()->route('admin.categories.index', ['type' => $category->type])
            ->with('success', 'Danh mục đã được cập nhật thành công.');
    }
    
    public function destroy(Category $category)
    {
        $type = $category->type;
        $category->delete();
        
        return redirect()->route('admin.categories.index', ['type' => $type])
            ->with('success', 'Danh mục đã được xóa thành công.');
    }
}
