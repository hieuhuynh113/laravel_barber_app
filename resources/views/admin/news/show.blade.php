@extends('layouts.admin')

@section('title', 'Chi tiết bài viết')

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết bài viết</h1>
        <div>
            <a href="{{ route('admin.news.edit', $news->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Chỉnh sửa
            </a>
            <a href="{{ route('admin.news.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Nội dung bài viết</h6>
                </div>
                <div class="card-body">
                    <h2 class="h4 mb-3">{{ $news->title }}</h2>
                    
                    @if($news->image)
                        <div class="text-center mb-4">
                            <img src="{{ asset('storage/' . $news->image) }}" alt="{{ $news->title }}" class="img-fluid rounded" style="max-height: 400px;">
                        </div>
                    @endif
                    
                    <div class="my-4">
                        {!! $news->content !!}
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin bài viết</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th style="width: 40%;">ID</th>
                                <td>{{ $news->id }}</td>
                            </tr>
                            <tr>
                                <th>Danh mục</th>
                                <td>{{ $news->category->name ?? 'Không có' }}</td>
                            </tr>
                            <tr>
                                <th>Tác giả</th>
                                <td>{{ $news->user->name ?? 'Không có' }}</td>
                            </tr>
                            <tr>
                                <th>Slug</th>
                                <td>{{ $news->slug }}</td>
                            </tr>
                            <tr>
                                <th>Lượt xem</th>
                                <td>{{ $news->view_count }}</td>
                            </tr>
                            <tr>
                                <th>Nổi bật</th>
                                <td>
                                    @if($news->is_featured)
                                        <span class="badge bg-warning">Có</span>
                                    @else
                                        <span class="badge bg-secondary">Không</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Trạng thái</th>
                                <td>
                                    @if($news->status)
                                        <span class="badge bg-success">Đã xuất bản</span>
                                    @else
                                        <span class="badge bg-warning">Bản nháp</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Ngày tạo</th>
                                <td>{{ $news->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Cập nhật lần cuối</th>
                                <td>{{ $news->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="mt-3">
                        <form action="{{ route('admin.news.destroy', $news->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa bài viết này?')">
                                <i class="fas fa-trash"></i> Xóa bài viết
                            </button>
                        </form>
                        
                        <form action="{{ route('admin.news.toggleFeatured', $news->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm">
                                <i class="fas fa-star"></i> {{ $news->is_featured ? 'Bỏ nổi bật' : 'Đánh dấu nổi bật' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin SEO</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th style="width: 40%;">Meta Title</th>
                                <td>{{ $news->meta_title ?? $news->title }}</td>
                            </tr>
                            <tr>
                                <th>Meta Description</th>
                                <td>{{ $news->meta_description ?? 'Không có' }}</td>
                            </tr>
                            <tr>
                                <th>Meta Keywords</th>
                                <td>{{ $news->meta_keywords ?? 'Không có' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 