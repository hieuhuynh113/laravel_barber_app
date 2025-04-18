@extends('layouts.admin')

@section('title', 'Thống kê đánh giá')

@section('styles')
<style>
    .star-rating {
        color: #ffc107;
    }
    .rating-progress {
        height: 10px;
        margin-bottom: 10px;
    }
    .rating-count {
        min-width: 30px;
        text-align: right;
    }
    .rating-percentage {
        min-width: 50px;
        text-align: right;
    }
    .stats-card {
        transition: all 0.3s;
    }
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .stats-icon {
        font-size: 2rem;
        color: #4e73df;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thống kê đánh giá</h1>
        <a href="{{ route('admin.reviews.index') }}" class="btn btn-primary">
            <i class="fas fa-list"></i> Danh sách đánh giá
        </a>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 stats-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng số đánh giá</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalReviews }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-comments fa-2x stats-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 stats-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Điểm đánh giá trung bình</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($averageRating, 1) }}
                                <span class="star-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= round($averageRating))
                                            <i class="fas fa-star"></i>
                                        @elseif($i - 0.5 <= $averageRating)
                                            <i class="fas fa-star-half-alt"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x stats-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2 stats-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Tỷ lệ đánh giá tích cực (4-5 sao)</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                        @php
                                            $positiveRatings = $ratingDistribution[4]['count'] + $ratingDistribution[5]['count'];
                                            $positivePercentage = $totalReviews > 0 ? round(($positiveRatings / $totalReviews) * 100) : 0;
                                        @endphp
                                        {{ $positivePercentage }}%
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $positivePercentage }}%"
                                            aria-valuenow="{{ $positivePercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-thumbs-up fa-2x stats-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Phân bố đánh giá theo số sao -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Phân bố đánh giá theo số sao</h6>
                </div>
                <div class="card-body">
                    @foreach($ratingDistribution as $rating => $data)
                        <div class="d-flex align-items-center mb-2">
                            <div class="rating-count me-2">{{ $rating }}</div>
                            <div class="star-rating me-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="{{ $i <= $rating ? 'fas' : 'far' }} fa-star"></i>
                                @endfor
                            </div>
                            <div class="flex-grow-1 mx-2">
                                <div class="progress rating-progress">
                                    <div class="progress-bar bg-{{ $rating >= 4 ? 'success' : ($rating >= 3 ? 'info' : ($rating >= 2 ? 'warning' : 'danger')) }}"
                                        role="progressbar" style="width: {{ $data['percentage'] }}%"
                                        aria-valuenow="{{ $data['percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <div class="rating-count me-2">{{ $data['count'] }}</div>
                            <div class="rating-percentage">({{ $data['percentage'] }}%)</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Dịch vụ được đánh giá cao nhất -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Dịch vụ được đánh giá cao nhất</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Dịch vụ</th>
                                    <th>Số đánh giá</th>
                                    <th>Điểm trung bình</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($serviceStats as $service)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.services.edit', $service->id) }}">
                                                {{ $service->name }}
                                            </a>
                                        </td>
                                        <td>{{ $service->reviews_count }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="me-2">{{ number_format($service->reviews_avg_rating, 1) }}</span>
                                                <div class="star-rating">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= round($service->reviews_avg_rating))
                                                            <i class="fas fa-star"></i>
                                                        @elseif($i - 0.5 <= $service->reviews_avg_rating)
                                                            <i class="fas fa-star-half-alt"></i>
                                                        @else
                                                            <i class="far fa-star"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Thợ cắt tóc được đánh giá cao nhất -->
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thợ cắt tóc được đánh giá cao nhất</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Thợ cắt tóc</th>
                                    <th>Số đánh giá</th>
                                    <th>Điểm trung bình</th>
                                    <th>Tỷ lệ 5 sao</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($barberStats as $barber)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ get_user_avatar($barber->user, 'small') }}" alt="{{ $barber->user->name }}" class="rounded-circle me-2" width="40" height="40">
                                                <a href="{{ route('admin.barbers.show', $barber->user->id) }}">
                                                    {{ $barber->user->name }}
                                                </a>
                                            </div>
                                        </td>
                                        <td>{{ $barber->reviews_count }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="me-2">{{ number_format($barber->reviews_avg_rating, 1) }}</span>
                                                <div class="star-rating">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= round($barber->reviews_avg_rating))
                                                            <i class="fas fa-star"></i>
                                                        @elseif($i - 0.5 <= $barber->reviews_avg_rating)
                                                            <i class="fas fa-star-half-alt"></i>
                                                        @else
                                                            <i class="far fa-star"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $fiveStarCount = \App\Models\Review::where('barber_id', $barber->id)->where('rating', 5)->count();
                                                $fiveStarPercentage = $barber->reviews_count > 0 ? round(($fiveStarCount / $barber->reviews_count) * 100) : 0;
                                            @endphp
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 me-2" style="height: 10px;">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $fiveStarPercentage }}%"
                                                        aria-valuenow="{{ $fiveStarPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <span>{{ $fiveStarPercentage }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
