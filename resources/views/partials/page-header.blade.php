<div class="page-header position-relative overflow-hidden py-5 full-width-banner">
    <div class="page-header-bg-container position-absolute top-0 left-0 w-100 h-100">
        <div class="overlay-dark position-absolute top-0 left-0 w-100 h-100 bg-dark opacity-75"></div>
        <div class="bg-image position-absolute top-0 left-0 w-100 h-100" style="background-image: url('{{ asset($backgroundImage ?? 'images/about-banner.jpg') }}'); background-size: cover; background-position: center; z-index: -1;"></div>
    </div>
    <div class="container position-relative z-index-1">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h1 class="display-3 fw-bold text-white mb-3 animated fadeInUp">{{ $title }}</h1>
                @if(isset($description))
                <p class="lead text-white-75 mb-4 animated fadeInUp mx-auto" style="animation-delay: 0.1s;">{{ $description }}</p>
                @endif
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent p-0 mb-0 justify-content-center">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-75 text-decoration-none">Trang chủ</a></li>
                        @if(isset($parentPage))
                        <li class="breadcrumb-item"><a href="{{ $parentPageUrl }}" class="text-white-75 text-decoration-none">{{ $parentPage }}</a></li>
                        @endif
                        <li class="breadcrumb-item active text-white fw-semibold" aria-current="page">{{ $title }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div> 