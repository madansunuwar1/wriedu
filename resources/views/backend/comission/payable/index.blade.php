@extends('layouts.admin')

@include('backend.script.session')
@include('backend.script.alert')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
    Swal.fire({
        title: 'Success!',
        text: "{{ session('success') }}",
        icon: 'success',
        confirmButtonText: 'OK',
        customClass: {
            popup: 'swal-custom-popup',
            confirmButton: 'swal-custom-ok-button'
        }
    });
</script>
@endif

<div class="main-container">
    <div class="widget-content searchable-container list">
        <div class="card card-body">
            <div class="row">
                <div class="col-md-12 col-xl-12">
                    <div class="text-[24px] text-[#2e7d32] font-semibold font-g">Commission</div>
                </div>
                <div class="col-md-6 col-xl-8">
                    <div class="position-relative">
                        <input type="text" class="form-control product-search ps-5" id="searchInput" onkeyup="filterTable()" placeholder="Search Finances...">
                        <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
                    </div>
                </div>
                <div class="col-md-3 col-xl-2 text-end d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
                    <button onclick="openModal()" class="btn btn-primary d-flex align-items-center">
                        <i class="ti ti-upload text-white me-1 fs-5"></i> Import CSV
                    </button>
                </div>
                <div class="col-md-3 col-xl-2 text-end d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
                    <button onclick="downloadData()" class="btn btn-primary d-flex align-items-center">
                        <i class="ti ti-download text-white me-1 fs-5"></i> Download Data
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive mb-4 border rounded-1">
            <table class="table text-nowrap mb-0 align-middle" id="dataTable">
                <thead class="text-dark fs-4">
                    <tr>
                        <th>ID</th>
                        <th>University</th>
                        <th>Product</th>
                        <th>Partner</th>
                        <th>Commission</th>
                        <th>Bouns</th>
                        <th>Intensive</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($commission_payable as $commission)
                    <tr>
                        <td>{{ $commission->id }}</td>
                        <td>{{ $commission->university }}</td>
                        <td>{{ $commission->product }}</td>
                        <td>{{ $commission->partner }}</td>

                        <td>
                            @if(is_null($commission->commission_types))
                                N/A
                            @elseif(is_array($commission->commission_types))
                                @foreach($commission->commission_types as $type => $data)
                                    @if(is_array($data) && isset($data['value']))
                                        {{ $type }} ({{ $data['value'] }})@if (!$loop->last), @endif
                                    @else
                                        {{ $type }} ({{ $data }})@if (!$loop->last), @endif
                                    @endif
                                @endforeach
                            @elseif(is_string($commission->commission_types))
                                {{ $commission->commission_types }}
                            @else
                                Invalid Data
                            @endif
                        </td>

                        <td>{{ $commission->bonus_commission == 0 ? 'No' : 'Yes' }}</td>
                        <td>{{ $commission->intensive_commission == 0 ? 'No' : 'Yes' }}</td>

                        <td>
                            <div class="dropdown dropstart">
                                <a href="javascript:void(0)" class="text-muted" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ti ti-dots-vertical fs-6"></i>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item d-flex align-items-center gap-3" href="{{ route('backend.commission.payable.record', $commission->id) }}">
                                        <i class="fs-4 ti ti-file-text"></i> Views
                                    </a>
                                   
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection