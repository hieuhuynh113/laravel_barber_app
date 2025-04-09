@extends('layouts.admin')

@section('title', 'Lịch hẹn theo lịch')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />
<style>
    .fc-event {
        cursor: pointer;
    }
    .barber-filter {
        margin-bottom: 15px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Lịch hẹn theo lịch</h1>
        <div>
            <a href="{{ route('admin.appointments.index') }}" class="btn btn-secondary">
                <i class="fas fa-list"></i> Danh sách lịch hẹn
            </a>
            <a href="{{ route('admin.appointments.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm lịch hẹn
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lịch hẹn</h6>
        </div>
        <div class="card-body">
            <div class="barber-filter">
                <div class="form-group">
                    <label for="barber_filter">Lọc theo thợ cắt tóc:</label>
                    <select id="barber_filter" class="form-select">
                        <option value="">Tất cả thợ cắt tóc</option>
                        @foreach($barbers as $barber)
                            <option value="{{ $barber->barber->id }}">{{ $barber->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div id="calendar"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/vi.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var barberFilter = document.getElementById('barber_filter');
        
        var calendar = new FullCalendar.Calendar(calendarEl, {
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            locale: 'vi',
            initialView: 'dayGridMonth',
            navLinks: true,
            dayMaxEvents: true,
            events: function(info, successCallback, failureCallback) {
                var params = {
                    start: info.startStr,
                    end: info.endStr
                };
                
                if (barberFilter.value) {
                    params.barber_id = barberFilter.value;
                }
                
                fetch("{{ route('admin.appointments.getAppointments') }}?" + new URLSearchParams(params))
                    .then(response => response.json())
                    .then(data => {
                        successCallback(data);
                    })
                    .catch(error => {
                        console.error('Error loading events:', error);
                        failureCallback(error);
                    });
            },
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                meridiem: false
            }
        });
        
        calendar.render();
        
        // Reload events when barber filter changes
        barberFilter.addEventListener('change', function() {
            calendar.refetchEvents();
        });
    });
</script>
@endsection 