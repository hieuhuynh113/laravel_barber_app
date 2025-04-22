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
            $query->where('name', 'like', "%{$search}%");
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
        // Thứ tự ưu tiên: session > request > mặc định
        $type = 'service'; // Mặc định

        // Kiểm tra nếu có trong request
        if ($request->has('type')) {
            $type = $request->input('type');
        }

        // Kiểm tra nếu có trong session (uu tiên cao nhất)
        if (session()->has('type')) {
            $type = session('type');
            session()->forget('type'); // Xóa khỏi session sau khi sử dụng
        }

        return view('admin.categories.create', compact('type'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    // Kiểm tra trùng lặp tên danh mục trong cùng một loại
                    $exists = Category::where('name', $value)
                        ->where('type', $request->type)
                        ->exists();

                    if ($exists) {
                        $fail('Tên danh mục đã tồn tại trong loại này.');
                    }
                },
            ],
            'type' => 'required|in:service,product,news',
            'status' => 'required|boolean',
        ]);

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);

        $category = Category::create([
            'name' => $request->name,
            'slug' => $slug,
            'type' => $request->type,
            'status' => $request->status,
        ]);

        if ($request->action === 'save_and_new') {
            // Khi lưu và tạo mới, giữ nguyên loại danh mục đã chọn
            return redirect()->route('admin.categories.create')
                ->with('type', $request->type) // Truyền loại danh mục qua session
                ->with('success', 'Danh mục "' . $category->name . '" đã được tạo thành công!');
        }

        return redirect()->route('admin.categories.index', ['type' => $request->type])
            ->with('success', 'Danh mục "' . $category->name . '" đã được tạo thành công!');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request, $category) {
                    // Kiểm tra trùng lặp tên danh mục trong cùng một loại, ngoại trừ danh mục hiện tại
                    $exists = Category::where('name', $value)
                        ->where('type', $category->type)
                        ->where('id', '!=', $category->id)
                        ->exists();

                    if ($exists) {
                        $fail('Tên danh mục đã tồn tại trong loại này.');
                    }
                },
            ],
            'status' => 'required|boolean',
        ]);

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);

        $category->update([
            'name' => $request->name,
            'slug' => $slug,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.categories.index', ['type' => $category->type])
            ->with('success', 'Danh mục "' . $category->name . '" đã được cập nhật thành công!');
    }

    public function destroy(Category $category)
    {
        $type = $category->type;
        $category->delete();

        return redirect()->route('admin.categories.index', ['type' => $type])
            ->with('success', 'Danh mục đã được xóa thành công.');
    }
}
