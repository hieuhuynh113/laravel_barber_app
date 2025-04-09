@extends('layouts.admin')

@section('title', 'Chỉnh sửa lịch hẹn #' . $appointment->id)

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chỉnh sửa lịch hẹn #{{ $appointment->id }}</h1>
        <div>
            <a href="{{ route('admin.appointments.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <a href="{{ route('admin.appointments.show', $appointment->id) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> Xem chi tiết
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Thông tin lịch hẹn</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.appointments.update', $appointment->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="user_id" class="form-label">Khách hàng <span class="text-danger">*</span></label>
                        <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                            <option value="">Chọn khách hàng</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('user_id', $appointment->user_id) == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }} ({{ $customer->phone ?? $customer->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="barber_id" class="form-label">Thợ cắt tóc <span class="text-danger">*</span></label>
                        <select name="barber_id" id="barber_id" class="form-select @error('barber_id') is-invalid @enderror" required>
                            <option value="">Chọn thợ cắt tóc</option>
                            @foreach($barbers as $barber)
                                <option value="{{ $barber->barber->id }}" {{ old('barber_id', $appointment->barber_id) == $barber->barber->id ? 'selected' : '' }}>
                                    {{ $barber->name }} ({{ $barber->barber->specialties ?? 'Không có chuyên môn' }})
                                </option>
                            @endforeach
                        </select>
                        @error('barber_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="appointment_date" class="form-label">Ngày hẹn <span class="text-danger">*</span></label>
                        <input type="date" name="appointment_date" id="appointment_date" class="form-control @error('appointment_date') is-invalid @enderror" 
                               value="{{ old('appointment_date', $appointment->appointment_date) }}" required>
                        @error('appointment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="appointment_time" class="form-label">Giờ hẹn <span class="text-danger">*</span></label>
                        <input type="time" name="appointment_time" id="appointment_time" class="form-control @error('appointment_time') is-invalid @enderror" 
                               value="{{ old('appointment_time', substr($appointment->appointment_time, 0, 5)) }}" required>
                        @error('appointment_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Dịch vụ <span class="text-danger">*</span></label>
                    <div class="row">
                        @foreach($services as $service)
                            <div class="col-md-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="service_ids[]" 
                                           value="{{ $service->id }}" id="service_{{ $service->id }}"
                                           {{ (old('service_ids') && in_array($service->id, old('service_ids'))) || 
                                              (is_array($selectedServices) && in_array($service->id, $selectedServices)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="service_{{ $service->id }}">
                                        {{ $service->name }} ({{ number_format($service->price) }} VNĐ, {{ $service->duration }} phút)
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('service_ids')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="note" class="form-label">Ghi chú</label>
                    <textarea name="note" id="note" class="form-control @error('note') is-invalid @enderror" rows="3">{{ old('note', $appointment->note) }}</textarea>
                    @error('note')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="pending" {{ old('status', $appointment->status) == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                        <option value="confirmed" {{ old('status', $appointment->status) == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                        <option value="completed" {{ old('status', $appointment->status) == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                        <option value="canceled" {{ old('status', $appointment->status) == 'canceled' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 