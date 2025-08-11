@extends('layouts.admin')

@section('title', 'User Activity Index')

@section('content')
    <div class="container-fluid px-4 py-4">
        <!-- Header Card -->
        
        <!-- Filter & Search Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body py-3">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-0 bg-light" placeholder="Search users..." id="userSearch">
                                </div>
                            </div>
                            <div class="col-md-6 text-md-end mt-2 mt-md-0">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-outline-primary active" data-filter="all">
                                        <i class="fas fa-users me-1"></i>All Users
                                    </button>
                                    <button class="btn btn-outline-success" data-filter="active">
                                        <i class="fas fa-circle me-1"></i>Active
                                    </button>
                                    <button class="btn btn-outline-warning" data-filter="inactive">
                                        <i class="fas fa-pause-circle me-1"></i>Inactive
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($users->count() > 0)
            <!-- Users Grid -->
            <div class="row" id="usersGrid">
                @foreach ($users as $index => $user)
                    <div class="col-xl-4 col-lg-6 col-md-6 mb-4 user-card" data-status="{{ $user->is_online ? 'active' : 'inactive' }}">
                        <div class="card border-0 shadow-sm h-100 user-item">
                            <div class="card-body p-4">
                                <!-- User Header -->
                                <div class="d-flex align-items-center mb-3">
                                    <div class="position-relative">
                                        <div class="avatar-lg bg-gradient-{{ $index % 4 == 0 ? 'primary' : ($index % 4 == 1 ? 'success' : ($index % 4 == 2 ? 'info' : 'warning')) }} text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                            <span class="fw-bold fs-5">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                        </div>
                                        <span class="position-absolute bottom-0 end-0 {{ $user->is_online ? 'bg-success' : 'bg-secondary' }} rounded-circle status-dot"></span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="card-title mb-1 text-dark fw-semibold">{{ $user->name }}</h5>
                                        <p class="text-muted small mb-0">
                                            <i class="fas fa-envelope me-1"></i>
                                            {{ $user->email ?? 'No email provided' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Activity Stats -->
                                <div class="row g-2 mb-3">
                                    <div class="col-4">
                                        <div class="text-center p-2 bg-light rounded-3">
                                            <div class="h6 mb-0 text-primary fw-bold">{{ $activityStats['created'] ?? 0 }}</div>
                                            <small class="text-muted">Created</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-center p-2 bg-light rounded-3">
                                            <div class="h6 mb-0 text-success fw-bold">{{ $activityStats['commented'] ?? 0 }}</div>
                                            <small class="text-muted">Commented</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-center p-2 bg-light rounded-3">
                                            <div class="h6 mb-0 text-info fw-bold">{{ $activityStats['updated'] ?? 0 }}</div>
                                            <small class="text-muted">Status</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Last Activity -->
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        Last seen: {{ $user->last_activity ? $user->last_activity->diffForHumans() : 'Never' }}
                                    </small>
                                    <span class="badge {{ $user->is_online ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} rounded-pill px-2 py-1">
                                        <i class="fas fa-circle me-1" style="font-size: 6px;"></i>{{ $user->is_online ? 'Online' : 'Offline' }}
                                    </span>
                                </div>

                                <!-- Action Button -->
                                <div class="d-grid">
                                    <a href="{{ route('backend.activity.record', ['user' => $user->id]) }}" 
                                       class="btn btn-primary btn-lg rounded-3">
                                        <i class="fas fa-chart-bar me-2"></i>
                                        View Activity Report
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination Card -->
            @if($users instanceof Illuminate\Pagination\AbstractPaginator && method_exists($users, 'links'))
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-center">
                                    {{ $users->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        @else
            <!-- Empty State Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <div class="empty-state-container">
                                <div class="empty-icon-circle bg-light rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center">
                                    <i class="fas fa-user-slash text-muted fs-1"></i>
                                </div>
                                <h3 class="text-dark mb-3 fw-semibold">No Users Found</h3>
                                <p class="text-muted mb-4 fs-6">
                                    It looks like there are no users in the system yet.<br>
                                    Get started by adding your first user or refresh to check again.
                                </p>
                                <div class="d-flex justify-content-center gap-3">
                                    <a href="{{ route('backend.users.create') ?? '#' }}" class="btn btn-primary btn-lg px-4 rounded-3">
                                        <i class="fas fa-plus me-2"></i>Add New User
                                    </a>
                                    <button class="btn btn-outline-secondary btn-lg px-4 rounded-3" onclick="location.reload()">
                                        <i class="fas fa-sync-alt me-2"></i>Refresh Page
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

       
    </div>

    <!-- Professional Custom Styles -->
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --info-gradient: linear-gradient(135deg, #667eea 0%, #f093fb 100%);
            --warning-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        body {
            background-color: #f8f9fc;
        }

        .bg-gradient-primary {
            background: var(--primary-gradient);
        }

        .bg-gradient-success {
            background: var(--success-gradient);
        }

        .bg-gradient-info {
            background: var(--info-gradient);
        }

        .bg-gradient-warning {
            background: var(--warning-gradient);
        }

        .card {
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
        }

        .avatar-lg {
            width: 60px;
            height: 60px;
            font-size: 18px;
        }

        .status-dot {
            width: 14px;
            height: 14px;
            border: 2px solid white;
        }

        .icon-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-icon {
            width: 70px;
            height: 70px;
        }

        .empty-icon-circle {
            width: 120px;
            height: 120px;
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .shadow-lg {
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1) !important;
        }

        .shadow-sm {
            box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.08) !important;
        }

        .user-card {
            transition: all 0.3s ease;
        }

        .bg-light {
            background-color: #f8f9fc !important;
        }

        .text-muted {
            color: #6c757d !important;
        }

        .badge {
            font-size: 0.75rem;
            font-weight: 500;
        }

        .bg-success-subtle {
            background-color: rgba(25, 135, 84, 0.1) !important;
        }

        .bg-secondary-subtle {
            background-color: rgba(108, 117, 125, 0.1) !important;
        }

        .text-success {
            color: #198754 !important;
        }

        .text-secondary {
            color: #6c757d !important;
        }

        @media (max-width: 768px) {
            .container-fluid {
                padding-left: 15px;
                padding-right: 15px;
            }
            
            .card-body {
                padding: 1.5rem !important;
            }
        }

        /* Smooth animations */
        .user-item {
            animation: fadeInUp 0.6s ease forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Search functionality styling */
        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            border-color: #667eea;
        }

        .input-group-text {
            border: none;
        }
    </style>

    <!-- Enhanced JavaScript with filter functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Simple search functionality
            const searchInput = document.getElementById('userSearch');
            const userCards = document.querySelectorAll('.user-card');

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    
                    userCards.forEach(card => {
                        const userName = card.querySelector('.card-title').textContent.toLowerCase();
                        const userEmail = card.querySelector('.text-muted').textContent.toLowerCase();
                        
                        if (userName.includes(searchTerm) || userEmail.includes(searchTerm)) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            }

            // Enhanced filter buttons functionality
            const filterButtons = document.querySelectorAll('[data-filter]');
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    const filter = this.dataset.filter;
                    
                    userCards.forEach(card => {
                        const status = card.dataset.status;
                        
                        if (filter === 'all') {
                            card.style.display = 'block';
                        } else if (filter === 'active' && status === 'active') {
                            card.style.display = 'block';
                        } else if (filter === 'inactive' && status === 'inactive') {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            });
        });
    </script>
@endsection