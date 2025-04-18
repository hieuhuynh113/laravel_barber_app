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
        $activeType = $request->input('type');
        $search = $request->input('search');

        // Lấy số lượng danh mục cho mỗi loại
        $serviceCategoriesCount = Category::where('type', 'service')->count();
        $productCategoriesCount = Category::where('type', 'product')->count();
        $newsCategoriesCount = Category::where('type', 'news')->count();

        // Nếu không có type hoặc type không hợp lệ, hiển thị tất cả danh mục
        if (!in_array($activeType, ['service', 'product', 'news'])) {
            $query = Category::query();
            $activeType = 'all';
        } else {
            $query = Category::where('type', $activeType);
        }

        // Tìm kiếm nếu có
        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        $categories = $query->latest()->paginate(10)->withQueryString();

        return view('admin.categories.index', compact(
            'categories',
            'activeType',
            'search',
            'serviceCategoriesCount',
            'productCategoriesCount',
            'newsCategoriesCount'
        ));
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
