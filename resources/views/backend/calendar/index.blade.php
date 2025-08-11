@extends('layouts.admin')
@section('content')

{{-- The old, separate Date Converter card has been removed. --}}

<div class="body-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                
                <!-- New Integrated Date Converter Dropdown (Left Side) -->
                <div class="dropdown">
                    <button class="btn btn-outline-primary" type="button" id="converterDropdownBtn" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                        <i class="fas fa-exchange-alt me-2"></i>Date Converter
                    </button>
                    <div class="dropdown-menu p-3 date-converter-dropdown" aria-labelledby="converterDropdownBtn">
                        <form id="dateConverterForm" onsubmit="return false;">
                            @csrf
                            <div class="btn-group w-100 mb-3" role="group">
                                <input type="radio" class="btn-check" name="conversionType" id="ad_to_bs" value="ad_to_bs" autocomplete="off" checked>
                                <label class="btn btn-outline-primary" for="ad_to_bs">AD to BS</label>

                                <input type="radio" class="btn-check" name="conversionType" id="bs_to_ad" value="bs_to_ad" autocomplete="off">
                                <label class="btn btn-outline-primary" for="bs_to_ad">BS to AD</label>
                            </div>

                            <div id="dateInputSection">
                                <div id="adDateInput" class="date-input-section">
                                    <label class="form-label fw-bold small"><i class="fas fa-calendar-alt me-1"></i>Gregorian Date (AD)</label>
                                    @php
                                        $currentAdYear = date('Y'); $startAdYear = $currentAdYear - 100; $endAdYear = $currentAdYear + 20;
                                        $adMonths = [
                                            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 
                                            7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
                                        ];
                                    @endphp
                                    <div class="input-group input-group-sm">
                                        <select id="adYear" class="form-select"><option value="" disabled>Year</option>
                                            @for ($i = $endAdYear; $i >= $startAdYear; $i--)
                                            <option value="{{ $i }}" {{ $i == $currentAdYear ? 'selected' : '' }}>{{ $i }}</option>
                                            @endfor
                                        </select>
                                        <select id="adMonth" class="form-select"><option value="" disabled>Month</option>
                                            @foreach($adMonths as $num => $name)
                                            <option value="{{ $num }}" {{ $num == date('n') ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        <select id="adDay" class="form-select"><option value="" disabled>Day</option>
                                            @for ($i = 1; $i <= 31; $i++)
                                            <option value="{{ $i }}" {{ $i == date('j') ? 'selected' : '' }}>{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                
                                <div id="bsDateInput" class="date-input-section" style="display: none;">
                                    <label class="form-label fw-bold small"><i class="fas fa-calendar me-1"></i>Nepali Date (BS)</label>
                                    @php
                                        $currentBsYear = (date('n') >= 4 && date('j') >= 14) ? date('Y') + 57 : date('Y') + 56;
                                        $startBsYear = $currentBsYear - 100; $endBsYear = $currentBsYear + 20;
                                        $bsMonths = [
                                            1 => 'Baishakh', 2 => 'Jestha', 3 => 'Ashadh', 4 => 'Shrawan', 5 => 'Bhadra', 6 => 'Ashwin', 
                                            7 => 'Kartik', 8 => 'Mangsir', 9 => 'Paush', 10 => 'Magh', 11 => 'Falgun', 12 => 'Chaitra'
                                        ];
                                    @endphp
                                    <div class="input-group input-group-sm">
                                        <select id="bsYear" class="form-select"><option value="" selected disabled>Year</option>
                                            @for ($i = $endBsYear; $i >= $startBsYear; $i--)<option value="{{ $i }}">{{ $i }}</option>@endfor
                                        </select>
                                        <select id="bsMonth" class="form-select"><option value="" selected disabled>Month</option>
                                            @foreach($bsMonths as $num => $name)<option value="{{ $num }}">{{ $name }}</option>@endforeach
                                        </select>
                                        <select id="bsDay" class="form-select"><option value="" selected disabled>Day</option>
                                            @for ($i = 1; $i <= 32; $i++)<option value="{{ $i }}">{{ $i }}</option>@endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid mt-3">
                                <button type="button" class="btn btn-primary" id="convertDate"><i class="fas fa-sync-alt me-1"></i> Convert</button>
                            </div>

                            <div class="mt-3" id="conversionResult"></div>

                            <hr>
                            
                        </form>
                    </div>
                </div>

                <!-- Calendar Title (Center) -->
                <h4 class="card-title mb-0 text-center mx-10">Calendar</h4>

                <!-- Calendar Navigation (Right Side) -->
                <div class="calendar-nav ms-auto">
                    <button class="btn btn-outline-primary btn-sm" id="prevMonth">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <span class="mx-3 text-dark fw-bold fs-5" id="currentMonthYear">{{ $calendarData['current_month'] }}</span>
                    <button class="btn btn-outline-primary btn-sm" id="nextMonth">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="nepali-calendar">
                    <div class="calendar-header">
                        @foreach($calendarData['day_names'] as $index => $dayName)
                            <div class="day-header {{ $index === 6 ? 'saturday' : '' }}">{{ $dayName }}</div>
                        @endforeach
                    </div>
                    <div class="calendar-grid" id="calendarGrid">
                        @foreach($calendarData['calendar'] as $week)
                            <div class="calendar-week">
                                @foreach($week as $day)
                                    @if($day)
                                        @php
                                            $isSaturday = date('D', strtotime($day['english_date'])) === 'Sat';
                                        @endphp
                                        <div class="calendar-day {{ $day['is_today'] ? 'today' : '' }} {{ $isSaturday ? 'holiday' : '' }}"
                                             data-date="{{ $day['english_date'] }}"
                                             data-nepali-date="{{ $day['nepali_full_date'] }}">
                                            <div class="day-events">
                                                @foreach($day['events'] as $event)
                                                    @php
                                                        $eventId = $event['id'] ?? '';
                                                        $eventTitle = $event['title'] ?? '';
                                                        $eventDescription = $event['description'] ?? '';
                                                        $eventStart = $event['start_date'] ?? '';
                                                        $eventEnd = $event['end_date'] ?? '';
                                                        $eventColor = $event['color'] ?? 'primary';
                                                        $eventImage = $event['image_url'] ?? '';
                                                    @endphp
                                                    <div class="event event-{{ $eventColor }}"
                                                         data-event-id="{{ $eventId }}"
                                                         data-event-title="{{ $eventTitle }}"
                                                         data-event-description="{{ $eventDescription }}"
                                                         data-event-start="{{ $eventStart }}"
                                                         data-event-end="{{ $eventEnd }}"
                                                         data-event-color="{{ $eventColor }}"
                                                         data-event-image="{{ $eventImage }}">
                                                        {{ $eventTitle }}
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="day-number">
                                                <span class="english-date">{{ date('j', strtotime($day['english_date'])) }}</span>
                                                <span class="nepali-date">{{ $day['nepali_date'] }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="calendar-day empty"></div>
                                    @endif
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="eventViewModal" tabindex="-1" aria-labelledby="eventViewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="eventViewModalLabel">Event Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="event-modal-body">
                        <div id="event-content"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Add style for the converter dropdown width */
.date-converter-dropdown {
    min-width: 380px;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
.nepali-calendar { background: white; }
.calendar-header { display: grid; grid-template-columns: repeat(7, 1fr); background: linear-gradient(135deg, #2ecc71, #27ae60, #1e8449); border: 1px solid #dee2e6; }
.day-header { padding: 12px 8px; text-align: center; font-weight: 600; border-right: 1px solid rgba(255, 255, 255, 0.2); color: #ffffff; }
.calendar-day.holiday, .calendar-day.holiday .nepali-date, .calendar-day.holiday .english-date { color: #dc3545 !important; }
.day-header.saturday { color: #dc3545; font-weight: bold; }
.day-header:last-child { border-right: none; }
.calendar-grid { border: 1px solid #dee2e6; border-top: none; }
.calendar-week { display: grid; grid-template-columns: repeat(7, 1fr); }
.calendar-day { min-height: 120px; border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6; padding: 8px; transition: background-color 0.2s; display: flex; flex-direction: column; }
.calendar-day:hover { background-color: #f8f9fa; }
.calendar-day.today { background-color: #e3f2fd; border: 2px solid #2196f3; }
.calendar-day.empty { background-color: #fafafa; cursor: default; }
.calendar-day:last-child { border-right: none; }
.mx-10 {
    margin-right: 50px !important;
    margin-left: 300px !important;
}
.day-events { flex: 0 0 auto; max-height: 60px; overflow-y: auto; margin-bottom: 8px; order: 1; }
.day-number { flex: 1; position: relative; display: flex; align-items: center; justify-content: center; order: 2; }
.nepali-date { font-size: 22px; font-weight: 600; color: #333; font-family: 'Noto Sans Devanagari', 'Mukti', 'Kalimati', sans-serif; text-align: center; }
.english-date { position: absolute; bottom: 2px; right: 2px; font-size: 12px; color: #666; }
.event { font-size: 11px; padding: 2px 4px; margin: 1px 0; border-radius: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; cursor: pointer; transition: all 0.2s ease; }
.event:hover { transform: translateY(-1px); box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.event-danger { color: #ff4757; background-color: rgba(255, 71, 87, 0.15); border: 1px solid rgba(255, 55, 66, 0.25); border-radius: 12px; padding: 0.5rem 1rem 0.5rem 1.5rem; font-weight: 500; position: relative; }
.event-danger::before { content: ""; position: absolute; left: 0px; top: 10%; width: 6px; height: 80%; background-color: #ff4757; border-radius: 999px; }
.event-success { color: #2ed573; background-color: rgba(46, 213, 115, 0.15); border: 1px solid rgba(38, 209, 101, 0.25); border-radius: 12px; padding: 0.5rem 1rem 0.5rem 1.5rem; font-weight: 500; position: relative; }
.event-success::before { content: ""; position: absolute; left: 0px; top: 10%; width: 6px; height: 80%; background-color: #2ed573; border-radius: 999px; }
.event-primary { color: #5352ed; background-color: rgba(83, 82, 237, 0.15); border: 1px solid rgba(71, 66, 220, 0.25); border-radius: 12px; padding: 0.5rem 1rem 0.5rem 1.5rem; font-weight: 500; position: relative; }
.event-primary::before { content: ""; position: absolute; left: 0px; top: 10%; width: 6px; height: 80%; background-color: #5352ed; border-radius: 999px; }
.event-warning { color: #ffa502; background-color: rgba(255, 165, 2, 0.15); border: 1px solid rgba(255, 149, 0, 0.25); border-radius: 12px; padding: 0.5rem 1rem 0.5rem 1.5rem; font-weight: 500; position: relative; }
.event-warning::before { content: ""; position: absolute; left: 0px; top: 10%; width: 6px; height: 80%; background-color: #ffa502; border-radius: 999px; }
.calendar-nav { display: flex; align-items: center; }
.event-image { max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); margin-bottom: 20px; display: block; }
.event-meta { background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
.event-meta .meta-row { display: flex; justify-content: space-between; margin-bottom: 8px; }
.event-meta .meta-row:last-child { margin-bottom: 0; }
.event-description { background-color: #ffffff; padding: 20px; border: 1px solid #dee2e6; border-radius: 8px; line-height: 1.6; font-size: 16px; }
.image-container { text-align: center; margin-bottom: 20px; position: relative; }
.image-placeholder { width: 100%; min-height: 200px; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; border: 2px dashed #dee2e6; border-radius: 8px; color: #6c757d; font-size: 14px; position: relative; }
.image-loading { background-color: #e3f2fd; color: #1976d2; border-color: #bbdefb; }
.image-error { background-color: #ffebee; color: #c62828; border-color: #ffcdd2; }
.image-success { background-color: #e8f5e8; color: #2e7d32; border-color: #c8e6c9; }
.spinner { display: inline-block; width: 20px; height: 20px; border: 3px solid #f3f3f3; border-top: 3px solid #1976d2; border-radius: 50%; animation: spin 1s linear infinite; margin-right: 10px; }
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
@media (max-width: 768px) {
    .calendar-day { min-height: 80px; padding: 4px; }
    .day-header { padding: 8px 4px; font-size: 12px; }
    .nepali-date { font-size: 18px; }
    .day-events { max-height: 40px; margin-bottom: 4px; }
    .modal-dialog.modal-xl { margin: 10px; max-width: calc(100% - 20px); }
    

.mx-10 {
    margin-right: 50px !important;
    margin-left: 50px !important;
}


}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // --- INTEGRATED DATE CONVERTER SCRIPT ---
    const resultDiv = document.getElementById('conversionResult');
    const convertBtn = document.getElementById('convertDate');
    const conversionTypeRadios = document.querySelectorAll('input[name="conversionType"]');
    
    const adDateInput = document.getElementById('adDateInput');
    const bsDateInput = document.getElementById('bsDateInput');
    
    const todayBtn = document.getElementById('todayBtn');
    const dashainBtn = document.getElementById('dashainBtn');
    const tiharBtn = document.getElementById('tiharBtn');

    const adYearSelect = document.getElementById('adYear');
    const adMonthSelect = document.getElementById('adMonth');
    const adDaySelect = document.getElementById('adDay');
    
    const bsYearSelect = document.getElementById('bsYear');
    const bsMonthSelect = document.getElementById('bsMonth');
    const bsDaySelect = document.getElementById('bsDay');

    // Handle switching between AD to BS and BS to AD
    conversionTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'ad_to_bs') {
                adDateInput.style.display = 'block';
                bsDateInput.style.display = 'none';
            } else {
                adDateInput.style.display = 'none';
                bsDateInput.style.display = 'block';
            }
            resultDiv.innerHTML = ''; // Clear results on switch
        });
    });

    // Handle the main convert button click
    convertBtn.addEventListener('click', function() {
        const selectedType = document.querySelector('input[name="conversionType"]:checked').value;
        if (selectedType === 'ad_to_bs') {
            convertAdToBs();
        } else {
            convertBsToAd();
        }
    });
    
    // Handle quick date buttons
    todayBtn.addEventListener('click', setTodayDate);
    dashainBtn.addEventListener('click', () => setQuickBsDate('2081-06-17', 'Dashain')); // Example date
    tiharBtn.addEventListener('click', () => setQuickBsDate('2081-07-15', 'Tihar')); // Example date

    // --- Core conversion functions ---
    function convertAdToBs() {
        const year = adYearSelect.value, month = adMonthSelect.value, day = adDaySelect.value;
        if (!year || !month || !day) {
            showWarning('Please select the full Gregorian (AD) date.'); return;
        }
        const adDate = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        showLoading('Converting AD to BS...');
        fetch("{{ route('convert.ad.to.bs') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
            body: JSON.stringify({ ad_date: adDate })
        })
        .then(res => res.json())
        .then(data => {
            if (data.converted_date) {
                showSuccess(`<strong>${data.converted_date} BS</strong>`);
            } else {
                showError(data.error || 'Conversion failed.');
            }
        }).catch(() => showError('An error occurred.'));
    }

    function convertBsToAd() {
        const year = bsYearSelect.value, month = bsMonthSelect.value, day = bsDaySelect.value;
        if (!year || !month || !day) {
            showWarning('Please select the full Nepali (BS) date.'); return;
        }
        const bsDate = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        showLoading('Converting BS to AD...');
        fetch("{{ route('convert.bs.to.ad') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
            body: JSON.stringify({ bs_date: bsDate })
        })
        .then(res => res.json())
        .then(data => {
            if (data.converted_date) {
                showInfo(`<strong>${data.converted_date} AD</strong>`);
            } else {
                showError(data.error || 'Invalid BS date.');
            }
        }).catch(() => showError('An error occurred.'));
    }
    
    // --- Helper functions ---
    function setTodayDate() {
        document.getElementById('ad_to_bs').checked = true;
        document.getElementById('ad_to_bs').dispatchEvent(new Event('change'));
        const today = new Date();
        adYearSelect.value = today.getFullYear();
        adMonthSelect.value = today.getMonth() + 1;
        adDaySelect.value = today.getDate();
        convertAdToBs();
    }
    
    function setQuickBsDate(bsDate) {
        document.getElementById('bs_to_ad').checked = true;
        document.getElementById('bs_to_ad').dispatchEvent(new Event('change'));
        const [year, month, day] = bsDate.split('-');
        bsYearSelect.value = parseInt(year, 10);
        bsMonthSelect.value = parseInt(month, 10);
        bsDaySelect.value = parseInt(day, 10);
        convertBsToAd();
    }

    // --- Message/Alert functions ---
    function showError(message) { resultDiv.innerHTML = `<div class="alert alert-danger alert-sm p-2 mt-2">${message}</div>`; }
    function showSuccess(message) { resultDiv.innerHTML = `<div class="alert alert-success alert-sm p-2 mt-2">${message}</div>`; }
    function showInfo(message) { resultDiv.innerHTML = `<div class="alert alert-info alert-sm p-2 mt-2">${message}</div>`; }
    function showWarning(message) { resultDiv.innerHTML = `<div class="alert alert-warning alert-sm p-2 mt-2">${message}</div>`; }
    function showLoading(message) { resultDiv.innerHTML = `<div class="alert alert-light alert-sm p-2 mt-2">${message}</div>`; }
});

// ========= CALENDAR SCRIPT (Unchanged) =========
document.addEventListener('DOMContentLoaded', function() {
    let currentYear = {{ $calendarData['current_year'] }};
    let currentMonth = {{ $calendarData['month_number'] }};

    document.getElementById('prevMonth').addEventListener('click', function() {
        currentMonth--;
        if (currentMonth < 1) {
            currentMonth = 12;
            currentYear--;
        }
        loadCalendarData();
    });

    document.getElementById('nextMonth').addEventListener('click', function() {
        currentMonth++;
        if (currentMonth > 12) {
            currentMonth = 1;
            currentYear++;
        }
        loadCalendarData();
    });

    function loadCalendarData() {
        fetch(`/calendar/month-data?year=${currentYear}&month=${currentMonth}`)
            .then(response => response.json())
            .then(data => {
                updateCalendarDisplay(data);
                document.getElementById('currentMonthYear').textContent = `${data.current_month}`;
            })
            .catch(error => console.error('Error loading calendar data:', error));
    }

    function updateCalendarDisplay(data) {
        const calendarGrid = document.getElementById('calendarGrid');
        let html = '';
        data.calendar.forEach(week => {
            html += '<div class="calendar-week">';
            week.forEach(day => {
                if (day) {
                    const todayClass = day.is_today ? 'today' : '';
                    const isSaturday = new Date(day.english_date).getDay() === 6;
                    const holidayClass = isSaturday ? 'holiday' : '';
                    let eventsHtml = '';
                    if (day.events && Array.isArray(day.events)) {
                        eventsHtml = day.events.map(event => {
                            let eventColor = 'primary';
                            let eventTitle = '';
                            let eventId = '';
                            let eventDescription = '';
                            let eventStart = '';
                            let eventEnd = '';
                            let eventImage = '';
                            if (typeof event === 'object' && event !== null) {
                                eventId = event.id || '';
                                eventColor = event.color || 'primary';
                                eventTitle = event.title || '';
                                eventDescription = event.description || '';
                                eventStart = event.start_date || '';
                                eventEnd = event.end_date || '';
                                eventImage = event.image_url || '';
                            } else if (typeof event === 'string') {
                                eventTitle = event;
                            }
                            return `<div class="event event-${eventColor}"
                                          data-event-id="${eventId}"
                                          data-event-title="${eventTitle}"
                                          data-event-description="${eventDescription}"
                                          data-event-start="${eventStart}"
                                          data-event-end="${eventEnd}"
                                          data-event-color="${eventColor}"
                                          data-event-image="${eventImage}">
                                          ${eventTitle}</div>`;
                        }).join('');
                    }
                    html += `
                        <div class="calendar-day ${todayClass} ${holidayClass}"
                             data-date="${day.english_date}"
                             data-nepali-date="${day.nepali_full_date}">
                            <div class="day-events">
                                ${eventsHtml}
                            </div>
                            <div class="day-number">
                                <span class="english-date">${new Date(day.english_date).getDate()}</span>
                                <span class="nepali-date">${day.nepali_date}</span>
                            </div>
                        </div>
                    `;
                } else {
                    html += '<div class="calendar-day empty"></div>';
                }
            });
            html += '</div>';
        });
        calendarGrid.innerHTML = html;
        attachEventClickListeners();
    }

    function attachEventClickListeners() {
        document.querySelectorAll('.event').forEach(eventElement => {
            eventElement.addEventListener('click', function(e) {
                e.stopPropagation();
                showEventDetails(this);
            });
        });
    }

    function showEventDetails(eventElement) {
        const eventTitle = eventElement.getAttribute('data-event-title');
        const eventDescription = eventElement.getAttribute('data-event-description');
        const eventStart = eventElement.getAttribute('data-event-start');
        const eventEnd = eventElement.getAttribute('data-event-end');
        const eventColor = eventElement.getAttribute('data-event-color');
        const eventImage = eventElement.getAttribute('data-event-image');
        const contentDiv = document.getElementById('event-content');
        let imageHtml = '';
        if (eventImage) {
            imageHtml = `
                <div class="image-container" id="imageContainer">
                    <div class="image-placeholder image-loading" id="imagePlaceholder">
                        <div class="spinner"></div>
                        <span>Loading image...</span>
                    </div>
                    <img src="${eventImage}"
                         alt="Event Image"
                         class="event-image"
                         style="display: none;"
                         onload="handleImageLoad(this)"
                         onerror="handleImageError(this)" />
                </div>
            `;
        } else {
            imageHtml = `
                <div class="image-container" id="imageContainer">
                    <div class="image-placeholder">
                        <i class="fas fa-image" style="font-size: 24px; margin-bottom: 10px;"></i>
                        <br>
                        <span>No image available</span>
                    </div>
                </div>
            `;
        }
        const eventHtml = `
            <div class="event-details">
                <h4 class="mb-4 text-primary">${eventTitle || 'Event Details'}</h4>
                ${imageHtml}
                <div class="event-meta">
                    <div class="meta-row">
                        <strong><i class="fas fa-tag me-2"></i>Event Type:</strong>
                        <span class="badge bg-${eventColor}">${capitalizeFirst(eventColor)}</span>
                    </div>
                    ${eventStart ? `
                    <div class="meta-row">
                        <strong><i class="fas fa-calendar-plus me-2"></i>Start Date:</strong>
                        <span>${formatDate(eventStart)}</span>
                    </div>
                    ` : ''}
                    ${eventEnd ? `
                    <div class="meta-row">
                        <strong><i class="fas fa-calendar-minus me-2"></i>End Date:</strong>
                        <span>${formatDate(eventEnd)}</span>
                    </div>
                    ` : ''}
                </div>
                ${eventDescription ? `
                <div class="event-description">
                    <h6 class="mb-3"><i class="fas fa-align-left me-2"></i>Description:</h6>
                    <div>${eventDescription.replace(/\n/g, '<br>')}</div>
                </div>
                ` : `
                <div class="event-description">
                    <p class="text-muted"><i class="fas fa-info-circle me-2"></i>No description available for this event.</p>
                </div>
                `}
            </div>
        `;
        document.getElementById('eventViewModalLabel').textContent = eventTitle || 'Event Details';
        contentDiv.innerHTML = eventHtml;
        const modal = new bootstrap.Modal(document.getElementById('eventViewModal'));
        modal.show();
    }

    window.handleImageLoad = function(imgElement) {
        const container = imgElement.closest('.image-container');
        if (container) {
            const placeholder = container.querySelector('.image-placeholder');
            if (placeholder) {
                placeholder.style.display = 'none';
                imgElement.style.display = 'block';
                imgElement.style.opacity = '0';
                imgElement.style.transition = 'opacity 0.3s ease-in-out';
                setTimeout(() => {
                    imgElement.style.opacity = '1';
                }, 100);
            }
        }
    };

    window.handleImageError = function(imgElement) {
        const container = imgElement.closest('.image-container');
        if (container) {
            const placeholder = container.querySelector('.image-placeholder');
            if (placeholder) {
                placeholder.className = 'image-placeholder image-error';
                placeholder.innerHTML = `
                    <i class="fas fa-exclamation-triangle" style="font-size: 24px; margin-bottom: 10px;"></i>
                    <br>
                    <span>⚠️ Image could not be loaded</span>
                    <br>
                    <small class="text-muted">Please check if the image file exists</small>
                `;
            }
            imgElement.style.display = 'none';
        }
    };

    function formatDate(dateString) {
        if (!dateString) return 'Not set';
        try {
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return 'Invalid date';
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch (error) {
            return 'Invalid date';
        }
    }

    function capitalizeFirst(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    attachEventClickListeners();
});
</script>
@endsection