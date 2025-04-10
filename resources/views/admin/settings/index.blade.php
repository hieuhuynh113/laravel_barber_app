@extends('layouts.admin')

@section('title', 'Cài đặt hệ thống')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Cài đặt hệ thống</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <ul class="nav nav-tabs card-header-tabs" id="settingsTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">
                        <i class="fas fa-cog mr-1"></i> Cài đặt chung
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">
                        <i class="fas fa-address-book mr-1"></i> Thông tin liên hệ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="social-tab" data-toggle="tab" href="#social" role="tab" aria-controls="social" aria-selected="false">
                        <i class="fas fa-share-alt mr-1"></i> Mạng xã hội
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="seo-tab" data-toggle="tab" href="#seo" role="tab" aria-controls="seo" aria-selected="false">
                        <i class="fas fa-search mr-1"></i> SEO
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="settingsTabContent">
                <!-- Cài đặt chung -->
                <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                    <form action="{{ route('admin.settings.updateGeneral') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="site_name">Tên trang web</label>
                                    <input type="text" class="form-control" id="site_name" name="site_name" value="{{ $generalSettings['site_name'] ?? '' }}">
                                </div>
                                <div class="form-group">
                                    <label for="site_description">Mô tả trang web</label>
                                    <textarea class="form-control" id="site_description" name="site_description" rows="3">{{ $generalSettings['site_description'] ?? '' }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="admin_email">Email quản trị</label>
                                    <input type="email" class="form-control" id="admin_email" name="admin_email" value="{{ $generalSettings['admin_email'] ?? '' }}">
                                </div>
                                <div class="form-group">
                                    <label for="working_hours">Giờ làm việc</label>
                                    <input type="text" class="form-control" id="working_hours" name="working_hours" value="{{ $generalSettings['working_hours'] ?? '' }}" placeholder="VD: 8:00 - 20:00">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="logo">Logo</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="logo" name="logo">
                                        <label class="custom-file-label" for="logo">Chọn file</label>
                                    </div>
                                    @if(isset($generalSettings['logo']) && $generalSettings['logo'])
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $generalSettings['logo']) }}" alt="Logo" class="img-thumbnail" style="max-height: 100px">
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="favicon">Favicon</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="favicon" name="favicon">
                                        <label class="custom-file-label" for="favicon">Chọn file</label>
                                    </div>
                                    @if(isset($generalSettings['favicon']) && $generalSettings['favicon'])
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $generalSettings['favicon']) }}" alt="Favicon" class="img-thumbnail" style="max-height: 50px">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Lưu cài đặt</button>
                    </form>
                </div>
                
                <!-- Thông tin liên hệ -->
                <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                    <form action="{{ route('admin.settings.updateContact') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_address">Địa chỉ</label>
                                    <input type="text" class="form-control" id="contact_address" name="contact_address" value="{{ $contactSettings['contact_address'] ?? '' }}">
                                </div>
                                <div class="form-group">
                                    <label for="contact_phone">Số điện thoại</label>
                                    <input type="text" class="form-control" id="contact_phone" name="contact_phone" value="{{ $contactSettings['contact_phone'] ?? '' }}">
                                </div>
                                <div class="form-group">
                                    <label for="contact_email">Email liên hệ</label>
                                    <input type="email" class="form-control" id="contact_email" name="contact_email" value="{{ $contactSettings['contact_email'] ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="google_map">Google Map Embed</label>
                                    <textarea class="form-control" id="google_map" name="google_map" rows="5">{{ $contactSettings['google_map'] ?? '' }}</textarea>
                                    <small class="form-text text-muted">Nhập mã nhúng iframe Google Maps.</small>
                                </div>
                                @if(isset($contactSettings['google_map']) && $contactSettings['google_map'])
                                    <div class="mt-2">
                                        <div class="embed-responsive embed-responsive-16by9">
                                            {!! $contactSettings['google_map'] !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Lưu cài đặt</button>
                    </form>
                </div>
                
                <!-- Mạng xã hội -->
                <div class="tab-pane fade" id="social" role="tabpanel" aria-labelledby="social-tab">
                    <form action="{{ route('admin.settings.updateSocial') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="facebook"><i class="fab fa-facebook text-primary mr-1"></i> Facebook</label>
                                    <input type="url" class="form-control" id="facebook" name="facebook" value="{{ $socialSettings['facebook'] ?? '' }}" placeholder="https://facebook.com/page">
                                </div>
                                <div class="form-group">
                                    <label for="twitter"><i class="fab fa-twitter text-info mr-1"></i> Twitter</label>
                                    <input type="url" class="form-control" id="twitter" name="twitter" value="{{ $socialSettings['twitter'] ?? '' }}" placeholder="https://twitter.com/account">
                                </div>
                                <div class="form-group">
                                    <label for="instagram"><i class="fab fa-instagram text-danger mr-1"></i> Instagram</label>
                                    <input type="url" class="form-control" id="instagram" name="instagram" value="{{ $socialSettings['instagram'] ?? '' }}" placeholder="https://instagram.com/account">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="youtube"><i class="fab fa-youtube text-danger mr-1"></i> Youtube</label>
                                    <input type="url" class="form-control" id="youtube" name="youtube" value="{{ $socialSettings['youtube'] ?? '' }}" placeholder="https://youtube.com/channel">
                                </div>
                                <div class="form-group">
                                    <label for="tiktok"><i class="fab fa-tiktok mr-1"></i> TikTok</label>
                                    <input type="url" class="form-control" id="tiktok" name="tiktok" value="{{ $socialSettings['tiktok'] ?? '' }}" placeholder="https://tiktok.com/@account">
                                </div>
                                <div class="form-group">
                                    <label for="linkedin"><i class="fab fa-linkedin text-primary mr-1"></i> LinkedIn</label>
                                    <input type="url" class="form-control" id="linkedin" name="linkedin" value="{{ $socialSettings['linkedin'] ?? '' }}" placeholder="https://linkedin.com/company/name">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Lưu cài đặt</button>
                    </form>
                </div>
                
                <!-- SEO -->
                <div class="tab-pane fade" id="seo" role="tabpanel" aria-labelledby="seo-tab">
                    <form action="{{ route('admin.settings.updateSeo') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="meta_title">Meta Title</label>
                                    <input type="text" class="form-control" id="meta_title" name="meta_title" value="{{ $seoSettings['meta_title'] ?? '' }}">
                                </div>
                                <div class="form-group">
                                    <label for="meta_description">Meta Description</label>
                                    <textarea class="form-control" id="meta_description" name="meta_description" rows="3">{{ $seoSettings['meta_description'] ?? '' }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="meta_keywords">Meta Keywords</label>
                                    <textarea class="form-control" id="meta_keywords" name="meta_keywords" rows="2">{{ $seoSettings['meta_keywords'] ?? '' }}</textarea>
                                    <small class="form-text text-muted">Các từ khóa phân tách bởi dấu phẩy.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="google_analytics">Google Analytics Code</label>
                                    <textarea class="form-control" id="google_analytics" name="google_analytics" rows="3">{{ $seoSettings['google_analytics'] ?? '' }}</textarea>
                                    <small class="form-text text-muted">Mã theo dõi Google Analytics.</small>
                                </div>
                                <div class="form-group">
                                    <label for="facebook_pixel">Facebook Pixel Code</label>
                                    <textarea class="form-control" id="facebook_pixel" name="facebook_pixel" rows="3">{{ $seoSettings['facebook_pixel'] ?? '' }}</textarea>
                                    <small class="form-text text-muted">Mã Facebook Pixel.</small>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Lưu cài đặt</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Hiển thị tên file đã chọn
    $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
    });
    
    // Xử lý chuyển tab dựa vào hash trong URL
    $(document).ready(function() {
        // Kích hoạt tab dựa trên hash trong URL
        var url = document.location.toString();
        if (url.match('#')) {
            var hash = url.split('#')[1];
            if ($('#' + hash).length) {
                $('.nav-tabs a[href="#' + hash + '"]').tab('show');
            }
        }
        
        // Cập nhật hash khi chuyển tab
        $('.nav-tabs a').on('click', function(e) {
            var scrollmem = $('body').scrollTop() || $('html').scrollTop();
            window.location.hash = this.hash;
            $('html,body').scrollTop(scrollmem);
        });
        
        // Kích hoạt tab khi hash trong URL thay đổi
        $(window).on('hashchange', function() {
            var hash = window.location.hash;
            if (hash) {
                $('.nav-tabs a[href="' + hash + '"]').tab('show');
            }
        });
    });
</script>
@endsection 