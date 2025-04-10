@extends('layouts.admin')

@section('title', 'Chi tiết liên hệ')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết liên hệ</h1>
        <a href="{{ route('admin.contacts.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Nội dung tin nhắn</h6>
                    <div>
                        @if($contact->status)
                            <form action="{{ route('admin.contacts.markAsUnread', $contact->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-sm">
                                    <i class="fas fa-envelope"></i> Đánh dấu chưa đọc
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.contacts.markAsRead', $contact->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fas fa-envelope-open"></i> Đánh dấu đã đọc
                                </button>
                            </form>
                        @endif
                        <form action="{{ route('admin.contacts.destroy', $contact->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa liên hệ này?')">
                                <i class="fas fa-trash"></i> Xóa
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4 pb-4 border-bottom">
                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="font-weight-bold">{{ $contact->subject }}</h5>
                            <span class="text-muted">{{ $contact->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <p class="mb-2"><strong>Từ:</strong> {{ $contact->name }} <{{ $contact->email }}></p>
                        @if($contact->phone)
                            <p class="mb-2"><strong>Số điện thoại:</strong> {{ $contact->phone }}</p>
                        @endif
                        <div class="mt-4">
                            <p>{!! nl2br(e($contact->message)) !!}</p>
                        </div>
                    </div>

                    @if(isset($contact->reply) && $contact->reply)
                        <div class="mb-4 pb-2">
                            <h6 class="font-weight-bold">Phản hồi của bạn:</h6>
                            <div class="mt-3 p-3 bg-light rounded">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="font-weight-bold">Admin</span>
                                    <span class="text-muted">{{ $contact->replied_at ? $contact->replied_at->format('d/m/Y H:i') : '' }}</span>
                                </div>
                                <p>{!! nl2br(e($contact->reply)) !!}</p>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('admin.contacts.reply', $contact->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="reply" class="font-weight-bold">Phản hồi</label>
                            <textarea class="form-control @error('reply') is-invalid @enderror" id="reply" name="reply" rows="5" required>{{ old('reply', $contact->reply ?? '') }}</textarea>
                            @error('reply')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Gửi phản hồi
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin người gửi</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Tên:</strong>
                            <span>{{ $contact->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Email:</strong>
                            <span>{{ $contact->email }}</span>
                        </li>
                        @if($contact->phone)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <strong>Số điện thoại:</strong>
                                <span>{{ $contact->phone }}</span>
                            </li>
                        @endif
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Ngày gửi:</strong>
                            <span>{{ $contact->created_at->format('d/m/Y H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Trạng thái:</strong>
                            @if($contact->status)
                                <span class="badge badge-success">Đã đọc</span>
                            @else
                                <span class="badge badge-warning">Chưa đọc</span>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 