@extends('dashboard')

@section('title', 'لوحة التحكم الرئيسية')

@section('head')
{{-- Clean SVG Icons - No External Dependencies --}}
@endsection

@push('styles')

<style>
    /* CSS Variables */
    :root {
        --primary: #0ea5e9;
        --primary-700: #0284c7;
        --primary-light: #e0f2fe;
        --accent: #10b981;
        --accent-light: #d1fae5;
        --warning: #f59e0b;
        --warning-light: #fef3c7;
        --danger: #ef4444;
        --danger-light: #fee2e2;
        --bg: #f8fafc;
        --card: #ffffff;
        --text: #334155;
        --muted: #64748b;
        --hover: rgba(14, 165, 233, .08);
        --active: rgba(14, 165, 233, .12);
        --success: #059669;
        --success-light: #ecfdf5;
    }

    /* Clean SVG Icons */
    .icon {
        display: inline-block;
        width: 1em;
        height: 1em;
        vertical-align: middle;
        fill: currentColor;
    }

    .icon-lg {
        width: 2rem;
        height: 2rem;
    }

    .icon-xl {
        width: 4rem;
        height: 4rem;
    }

    .hero-welcome {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-700) 50%, var(--success) 100%);
        border-radius: 20px;
        position: relative;
        overflow: hidden;
        color: white;
        box-shadow: 0 20px 40px rgba(14, 165, 233, 0.25);
    }

    .hero-welcome::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>') repeat;
        opacity: 0.1;
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    .hero-icon i {
        font-size: 4rem;
        color: #e0f2fe;
        text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    }

    .hero-title {
        font-size: 2.5rem;
        font-weight: 700;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        margin-bottom: 1rem;
    }

    .hero-subtitle {
        font-size: 1.2rem;
        opacity: 0.9;
        font-weight: 300;
    }

    .user-info {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        padding: 1.5rem;
        display: inline-block;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .user-greeting {
        font-size: 1.1rem;
        opacity: 0.9;
    }

    .user-name {
        font-size: 1.3rem;
        color: #e0f2fe;
        margin: 0 0.5rem;
    }

    .user-role-badge {
        background: rgba(224, 242, 254, 0.2);
        color: #e0f2fe;
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 600;
        border: 1px solid rgba(224, 242, 254, 0.3);
        margin-right: 0.5rem;
    }

    .stats-card {
        background: var(--card);
        border-radius: 20px;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        overflow: hidden;
        position: relative;
    }

    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary), var(--accent));
    }

    .stats-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        position: relative;
    }

    .stats-icon.primary {
        background: linear-gradient(135deg, #0ea5e9, #0284c7);
    }

    .stats-icon.success {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .stats-icon.warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .stats-icon.info {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    }

    .stats-icon.purple {
        background: linear-gradient(135deg, #f97316, #ea580c);
    }

    .stats-icon i {
        font-size: 2rem;
        color: white;
    }

    .stats-number {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 1rem 0;
    }

    .stats-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 1rem;
    }

    .info-card {
        background: var(--card);
        border-radius: 20px;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .info-card .card-header {
        background: linear-gradient(135deg, #0ea5e9, #059669);
        color: white;
        border-radius: 20px 20px 0 0;
        border: none;
    }

    .text-orange {
        color: #f97316 !important;
    }

    .btn-orange {
        background: linear-gradient(135deg, #f97316, #ea580c);
        border: none;
        color: white;
    }

    .btn-orange:hover {
        background: linear-gradient(135deg, #ea580c, #dc2626);
        color: white;
    }
</style>
@endpush

@section('content')
<!-- Hero Welcome Section -->
<div class="row mb-5">
    <div class="col-12">
        <div class="hero-welcome text-center py-5">
            <div class="hero-content">
                <div class="hero-icon mb-4">
                    <svg class="icon icon-xl" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                        <path d="M19 14.5v2c0 .83-.67 1.5-1.5 1.5h-11C5.67 18 5 17.33 5 16.5v-2c0-.83.67-1.5 1.5-1.5h11c.83 0 1.5.67 1.5 1.5z" />
                        <path d="M12 6v6m-3-3h6" />
                    </svg>
                </div>
                <h1 class="hero-title">مرحباً بك في نظام إدارة المستشفى الذكي</h1>
                <p class="hero-subtitle mb-4">منصة متكاملة لإدارة جميع عمليات المستشفى بكفاءة وذكاء</p>
                <div class="user-info">
                    <span class="user-greeting">أهلاً وسهلاً</span>
                    <strong class="user-name ">{{ Auth::user()->name }}</strong>
                    <br>
                    <span class="user-role-badge  p-1">
                        <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M12,7C13.4,7 14.8,8.6 14.8,10V11.5C14.8,12.4 14.4,13.2 13.7,13.7V16.5C13.7,17.1 13.3,17.5 12.7,17.5H11.3C10.7,17.5 10.3,17.1 10.3,16.5V13.7C9.6,13.2 9.2,12.4 9.2,11.5V10C9.2,8.6 10.6,7 12,7Z" />
                        </svg>
                        {{ Auth::user()->role->name ?? 'غير محدد' }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="row mb-5">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card card h-100">
            <div class="card-body p-4 text-center">
                <div class="stats-icon primary">
                    <svg class="icon icon-lg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zm4 18v-6h2.5l-2.54-7.63A1.5 1.5 0 0 0 18.54 7H17c-.8 0-1.5.7-1.5 1.5v6c0 .8.7 1.5 1.5 1.5h1v6h2zM12.5 11.5c.83 0 1.5-.67 1.5-1.5s-.67-1.5-1.5-1.5S11 9.17 11 10s.67 1.5 1.5 1.5zM5.5 6c1.11 0 2-.89 2-2s-.89-2-2-2-2 .89-2 2 .89 2 2 2zm2 16v-7H9V9c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v6H1.5v7h6zM12 13.5c-.83 0-1.5.67-1.5 1.5s.67 1.5 1.5 1.5 1.5-.67 1.5-1.5-.67-1.5-1.5-1.5z" />
                    </svg>
                </div>
                <h5 class="stats-title">المستخدمين</h5>
                <div class="stats-number text-primary">{{ \App\Models\User::count() }}</div>
                @can('viewAny', \App\Models\User::class)
                <a href="{{ route('users.index') }}" class="btn btn-primary btn-sm px-4">
                    <svg class="icon me-1" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.22 21.78,8.95 21.66,8.73L19.66,5.27C19.54,5.05 19.27,4.96 19.05,5.05L16.56,6.05C16.04,5.66 15.5,5.32 14.87,5.07L14.5,2.42C14.46,2.18 14.25,2 14,2H10C9.75,2 9.54,2.18 9.5,2.42L9.13,5.07C8.5,5.32 7.96,5.66 7.44,6.05L4.95,5.05C4.73,4.96 4.46,5.05 4.34,5.27L2.34,8.73C2.22,8.95 2.27,9.22 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.78 2.22,15.05 2.34,15.27L4.34,18.73C4.46,18.95 4.73,19.03 4.95,18.95L7.44,17.94C7.96,18.34 8.5,18.68 9.13,18.93L9.5,21.58C9.54,21.82 9.75,22 10,22H14C14.25,22 14.46,21.82 14.5,21.58L14.87,18.93C15.5,18.68 16.04,18.34 16.56,17.94L19.05,18.95C19.27,19.03 19.54,18.95 19.66,18.73L21.66,15.27C21.78,15.05 21.73,14.78 21.54,14.63L19.43,12.97Z" />
                    </svg>إدارة
                </a>
                @else
                <button class="btn btn-outline-secondary btn-sm" disabled>غير متاح</button>
                @endcan
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card card h-100">
            <div class="card-body p-4 text-center">
                <div class="stats-icon purple">
                    <svg class="icon icon-lg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M12,7C13.4,7 14.8,8.6 14.8,10V11.5C14.8,12.4 14.4,13.2 13.7,13.7V16.5C13.7,17.1 13.3,17.5 12.7,17.5H11.3C10.7,17.5 10.3,17.1 10.3,16.5V13.7C9.6,13.2 9.2,12.4 9.2,11.5V10C9.2,8.6 10.6,7 12,7Z" />
                    </svg>
                </div>
                <h5 class="stats-title">الأدوار</h5>
                <div class="stats-number text-orange">{{ \App\Models\Role::count() }}</div>
                <a href="{{ route('roles.index') }}" class="btn btn-orange btn-sm px-4">
                    <svg class="icon me-1" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.22 21.78,8.95 21.66,8.73L19.66,5.27C19.54,5.05 19.27,4.96 19.05,5.05L16.56,6.05C16.04,5.66 15.5,5.32 14.87,5.07L14.5,2.42C14.46,2.18 14.25,2 14,2H10C9.75,2 9.54,2.18 9.5,2.42L9.13,5.07C8.5,5.32 7.96,5.66 7.44,6.05L4.95,5.05C4.73,4.96 4.46,5.05 4.34,5.27L2.34,8.73C2.22,8.95 2.27,9.22 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.78 2.22,15.05 2.34,15.27L4.34,18.73C4.46,18.95 4.73,19.03 4.95,18.95L7.44,17.94C7.96,18.34 8.5,18.68 9.13,18.93L9.5,21.58C9.54,21.82 9.75,22 10,22H14C14.25,22 14.46,21.82 14.5,21.58L14.87,18.93C15.5,18.68 16.04,18.34 16.56,17.94L19.05,18.95C19.27,19.03 19.54,18.95 19.66,18.73L21.66,15.27C21.78,15.05 21.73,14.78 21.54,14.63L19.43,12.97Z" />
                    </svg>إدارة
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card card h-100">
            <div class="card-body p-4 text-center">
                <div class="stats-icon success">
                    <svg class="icon icon-lg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 5.5V7.5L12 8.5L9 7.5V5.5L3 7V9L9 10.5V12L3 13.5V15.5L9 14L12 15L15 14L21 15.5V13.5L15 12V10.5L21 9ZM12 10.5C10.9 10.5 10 11.4 10 12.5S10.9 14.5 12 14.5 14 13.6 14 12.5 13.1 10.5 12 10.5Z" />
                    </svg>
                </div>
                <h5 class="stats-title">المرضى</h5>
                <div class="stats-number text-success">{{ \App\Models\Patient::count() }}</div>
                <a href="{{ route('patients.index') }}" class="btn btn-success btn-sm px-4">
                    <svg class="icon me-1" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.22 21.78,8.95 21.66,8.73L19.66,5.27C19.54,5.05 19.27,4.96 19.05,5.05L16.56,6.05C16.04,5.66 15.5,5.32 14.87,5.07L14.5,2.42C14.46,2.18 14.25,2 14,2H10C9.75,2 9.54,2.18 9.5,2.42L9.13,5.07C8.5,5.32 7.96,5.66 7.44,6.05L4.95,5.05C4.73,4.96 4.46,5.05 4.34,5.27L2.34,8.73C2.22,8.95 2.27,9.22 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.78 2.22,15.05 2.34,15.27L4.34,18.73C4.46,18.95 4.73,19.03 4.95,18.95L7.44,17.94C7.96,18.34 8.5,18.68 9.13,18.93L9.5,21.58C9.54,21.82 9.75,22 10,22H14C14.25,22 14.46,21.82 14.5,21.58L14.87,18.93C15.5,18.68 16.04,18.34 16.56,17.94L19.05,18.95C19.27,19.03 19.54,18.95 19.66,18.73L21.66,15.27C21.78,15.05 21.73,14.78 21.54,14.63L19.43,12.97Z" />
                    </svg>إدارة
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card card h-100">
            <div class="card-body p-4 text-center">
                <div class="stats-icon warning">
                    <svg class="icon icon-lg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19.5 3.5L18.1 4.9C19.2 6 19.8 7.5 19.8 9.1C19.8 12.4 17.1 15.1 13.8 15.1S7.8 12.4 7.8 9.1C7.8 7.5 8.4 6 9.5 4.9L8.1 3.5C6.7 4.9 5.8 6.9 5.8 9.1C5.8 13.5 9.4 17.1 13.8 17.1S21.8 13.5 21.8 9.1C21.8 6.9 20.9 4.9 19.5 3.5M7 17.1C6.4 17.1 6 17.5 6 18.1V21C6 21.6 6.4 22 7 22S8 21.6 8 21V18.1C8 17.5 7.6 17.1 7 17.1Z" />
                    </svg>
                </div>
                <h5 class="stats-title">الزيارات الطبية</h5>
                <div class="stats-number text-warning">{{ \App\Models\Encounter::count() }}</div>
                <a href="{{ route('encounters.index') }}" class="btn btn-warning btn-sm px-4">
                    <svg class="icon me-1" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.22 21.78,8.95 21.66,8.73L19.66,5.27C19.54,5.05 19.27,4.96 19.05,5.05L16.56,6.05C16.04,5.66 15.5,5.32 14.87,5.07L14.5,2.42C14.46,2.18 14.25,2 14,2H10C9.75,2 9.54,2.18 9.5,2.42L9.13,5.07C8.5,5.32 7.96,5.66 7.44,6.05L4.95,5.05C4.73,4.96 4.46,5.05 4.34,5.27L2.34,8.73C2.22,8.95 2.27,9.22 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.78 2.22,15.05 2.34,15.27L4.34,18.73C4.46,18.95 4.73,19.03 4.95,18.95L7.44,17.94C7.96,18.34 8.5,18.68 9.13,18.93L9.5,21.58C9.54,21.82 9.75,22 10,22H14C14.25,22 14.46,21.82 14.5,21.58L14.87,18.93C15.5,18.68 16.04,18.34 16.56,17.94L19.05,18.95C19.27,19.03 19.54,18.95 19.66,18.73L21.66,15.27C21.78,15.05 21.73,14.78 21.54,14.63L19.43,12.97Z" />
                    </svg>إدارة
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card card h-100">
            <div class="card-body p-4 text-center">
                <div class="stats-icon info">
                    <svg class="icon icon-lg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M21.33 12.91C21.42 14.46 20.71 15.95 19.44 16.86L20.21 18.35C20.44 18.8 20.47 19.33 20.27 19.8C20.08 20.27 19.69 20.64 19.21 20.8L18.42 21.05C18.25 21.11 18.06 21.14 17.88 21.14C17.37 21.14 16.89 20.91 16.56 20.5L14.44 18C13.55 17.85 12.8 17.24 12.5 16.4C12.42 16.18 12.38 15.95 12.38 15.72C12.32 14.1 13.18 12.58 14.68 11.93C14.97 11.8 15.3 11.73 15.63 11.73C16.89 11.73 17.95 12.6 18.22 13.8C18.28 14.02 18.31 14.25 18.31 14.47C18.31 14.97 18.65 15.4 19.15 15.4C19.65 15.4 20 14.97 20 14.47C20 13.8 19.11 13.15 18.31 12.91C18.28 12.9 18.25 12.9 18.22 12.89C17.95 11.69 16.89 10.82 15.63 10.82C15.3 10.82 14.97 10.89 14.68 11.02C13.18 11.67 12.32 13.19 12.38 14.81V14.47C12.38 14.25 12.42 14.02 12.5 13.8C12.8 12.96 13.55 12.35 14.44 12.2L16.56 9.7C16.89 9.29 17.37 9.06 17.88 9.06C18.06 9.06 18.25 9.09 18.42 9.15L19.21 9.4C19.69 9.56 20.08 9.93 20.27 10.4C20.47 10.87 20.44 11.4 20.21 11.85L19.44 13.34C20.71 14.25 21.42 15.74 21.33 17.29M9.67 11.09C9.58 9.54 10.29 8.05 11.56 7.14L10.79 5.65C10.56 5.2 10.53 4.67 10.73 4.2C10.92 3.73 11.31 3.36 11.79 3.2L12.58 2.95C12.75 2.89 12.94 2.86 13.12 2.86C13.63 2.86 14.11 3.09 14.44 3.5L16.56 6C17.45 6.15 18.2 6.76 18.5 7.6C18.58 7.82 18.62 8.05 18.62 8.28C18.68 9.9 17.82 11.42 16.32 12.07C16.03 12.2 15.7 12.27 15.37 12.27C14.11 12.27 13.05 11.4 12.78 10.2C12.72 9.98 12.69 9.75 12.69 9.53C12.69 9.03 12.35 8.6 11.85 8.6C11.35 8.6 11 9.03 11 9.53C11 10.2 11.89 10.85 12.69 11.09C12.72 11.1 12.75 11.1 12.78 11.11C13.05 12.31 14.11 13.18 15.37 13.18C15.7 13.18 16.03 13.11 16.32 12.98C17.82 12.33 18.68 10.81 18.62 9.19V9.53C18.62 9.75 18.58 9.98 18.5 10.2C18.2 11.04 17.45 11.65 16.56 11.8L14.44 14.3C14.11 14.71 13.63 14.94 13.12 14.94C12.94 14.94 12.75 14.91 12.58 14.85L11.79 14.6C11.31 14.44 10.92 14.07 10.73 13.6C10.53 13.13 10.56 12.6 10.79 12.15L11.56 10.66C10.29 9.75 9.58 8.26 9.67 6.71" />
                    </svg>
                </div>
                <h5 class="stats-title">الصيدلية</h5>
                <div class="stats-number text-info">{{ \App\Models\Medicine::count() }}</div>
                <a href="{{ route('medicines.index') }}" class="btn btn-info btn-sm px-4">
                    <svg class="icon me-1" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.22 21.78,8.95 21.66,8.73L19.66,5.27C19.54,5.05 19.27,4.96 19.05,5.05L16.56,6.05C16.04,5.66 15.5,5.32 14.87,5.07L14.5,2.42C14.46,2.18 14.25,2 14,2H10C9.75,2 9.54,2.18 9.5,2.42L9.13,5.07C8.5,5.32 7.96,5.66 7.44,6.05L4.95,5.05C4.73,4.96 4.46,5.05 4.34,5.27L2.34,8.73C2.22,8.95 2.27,9.22 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.78 2.22,15.05 2.34,15.27L4.34,18.73C4.46,18.95 4.73,19.03 4.95,18.95L7.44,17.94C7.96,18.34 8.5,18.68 9.13,18.93L9.5,21.58C9.54,21.82 9.75,22 10,22H14C14.25,22 14.46,21.82 14.5,21.58L14.87,18.93C15.5,18.68 16.04,18.34 16.56,17.94L19.05,18.95C19.27,19.03 19.54,18.95 19.66,18.73L21.66,15.27C21.78,15.05 21.73,14.78 21.54,14.63L19.43,12.97Z" />
                    </svg>إدارة
                </a>
            </div>
        </div>
    </div>
</div>

<!-- System Info -->
<div class="row">
    <div class="col-12">
        <div class="info-card card">
            <div class="card-header p-4">
                <h5 class="mb-0">
                    <svg class="icon me-2" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M16,6L18.29,8.29L13.41,13.17L9.41,9.17L2,16.59L3.41,18L9.41,12L13.41,16L19.71,9.71L22,12V6H16Z" />
                    </svg>
                    حالة النظام والتقدم
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-success mb-3">
                            <svg class="icon me-2" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C6.5 2 2 6.5 2 12S6.5 22 12 22 22 17.5 22 12 17.5 2 12 2M10 17L5 12L6.41 10.59L10 14.17L17.59 6.58L19 8L10 17ZM12 10.5C10.9 10.5 10 11.4 10 12.5S10.9 14.5 12 14.5 14 13.6 14 12.5 13.1 10.5 12 10.5Z" />
                            </svg>
                            الميزات المكتملة
                        </h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <svg class="icon text-success me-2" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z" />
                                </svg>
                                نظام المصادقة والتفويض
                            </li>
                            <li class="mb-2">
                                <svg class="icon text-success me-2" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M5.5,6c1.11,0 2-.89 2-2s-.89-2-2-2-2,.89-2,2,.89,2 2,2zm2,16v-7H9V9c0-.55-.45-1-1-1H4c-.55,0-1,.45-1,1v6H1.5v7h6zM12,13.5c-.83,0-1.5.67-1.5,1.5s.67,1.5 1.5,1.5 1.5-.67 1.5-1.5-.67-1.5-1.5-1.5z" />
                                </svg>
                                إدارة المستخدمين والأدوار
                            </li>
                            <li class="mb-2">
                                <svg class="icon text-success me-2" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 5.5V7.5L12 8.5L9 7.5V5.5L3 7V9L9 10.5V12L3 13.5V15.5L9 14L12 15L15 14L21 15.5V13.5L15 12V10.5L21 9ZM12 10.5C10.9 10.5 10 11.4 10 12.5S10.9 14.5 12 14.5 14 13.6 14 12.5 13.1 10.5 12 10.5Z" />
                                </svg>
                                نظام إدارة المرضى
                            </li>
                            <li class="mb-2">
                                <svg class="icon text-success me-2" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19.5 3.5L18.1 4.9C19.2 6 19.8 7.5 19.8 9.1C19.8 12.4 17.1 15.1 13.8 15.1S7.8 12.4 7.8 9.1C7.8 7.5 8.4 6 9.5 4.9L8.1 3.5C6.7 4.9 5.8 6.9 5.8 9.1C5.8 13.5 9.4 17.1 13.8 17.1S21.8 13.5 21.8 9.1C21.8 6.9 20.9 4.9 19.5 3.5M7 17.1C6.4 17.1 6 17.5 6 18.1V21C6 21.6 6.4 22 7 22S8 21.6 8 21V18.1C8 17.5 7.6 17.1 7 17.1Z" />
                                </svg>
                                نظام إدارة الزيارات الطبية
                            </li>
                            <li class="mb-2">
                                <svg class="icon text-success me-2" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M6,13C6.55,13 7,13.45 7,14A1,1 0 0,1 6,15A1,1 0 0,1 5,14C5,13.45 5.45,13 6,13M6,17C6.55,17 7,17.45 7,18A1,1 0 0,1 6,19A1,1 0 0,1 5,18C5,17.45 5.45,17 6,17M6,9A1,1 0 0,1 7,10A1,1 0 0,1 6,11A1,1 0 0,1 5,10A1,1 0 0,1 6,9M3,5A2,2 0 0,1 5,3H19A2,2 0 0,1 21,5V19A2,2 0 0,1 19,21H5A2,2 0 0,1 3,19V5M9,7V9H19V7H9M9,11V13H19V11H9M9,15V17H19V15H9Z" />
                                </svg>
                                نظام إدارة الصيدلية
                            </li>
                            <li class="mb-2">
                                <svg class="icon text-success me-2" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.5,12A1.5,1.5 0 0,1 16,10.5A1.5,1.5 0 0,1 17.5,9A1.5,1.5 0 0,1 19,10.5A1.5,1.5 0 0,1 17.5,12M14.5,8A1.5,1.5 0 0,1 13,6.5A1.5,1.5 0 0,1 14.5,5A1.5,1.5 0 0,1 16,6.5A1.5,1.5 0 0,1 14.5,8M9.5,8A1.5,1.5 0 0,1 8,6.5A1.5,1.5 0 0,1 9.5,5A1.5,1.5 0 0,1 11,6.5A1.5,1.5 0 0,1 9.5,8M6.5,12A1.5,1.5 0 0,1 5,10.5A1.5,1.5 0 0,1 6.5,9A1.5,1.5 0 0,1 8,10.5A1.5,1.5 0 0,1 6.5,12M12,3A9,9 0 0,0 3,12A9,9 0 0,0 12,21A1.5,1.5 0 0,0 13.5,19.5C13.5,19.11 13.35,18.76 13.11,18.5C12.88,18.23 12.73,17.88 12.73,17.5A1.5,1.5 0 0,1 14.23,16H16A5,5 0 0,0 21,11C21,6.58 16.97,3 12,3Z" />
                                </svg>
                                واجهة مستخدم محترفة ومتجاوبة
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-warning mb-3">
                            <svg class="icon me-2" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M16.2,16.2L11,13V7H12.5V12.2L17,14.7L16.2,16.2Z" />
                            </svg>
                            قيد التطوير
                        </h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <svg class="icon text-warning me-2" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 5.5V7.5L12 8.5L9 7.5V5.5L3 7V9L9 10.5V12L3 13.5V15.5L9 14L12 15L15 14L21 15.5V13.5L15 12V10.5L21 9ZM12 10.5C10.9 10.5 10 11.4 10 12.5S10.9 14.5 12 14.5 14 13.6 14 12.5 13.1 10.5 12 10.5Z" />
                                </svg>
                                نظام إدارة المرضى
                            </li>
                            <li class="mb-2">
                                <svg class="icon text-warning me-2" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19,3H18V1H16V3H8V1H6V3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3M19,19H5V8H19V19M7,10V12H9V10H7M15,10V12H17V10H15M11,14V16H13V14H11M15,14V16H17V14H15Z" />
                                </svg>
                                إدارة الزيارات والمواعيد
                            </li>
                            <li class="mb-2">
                                <svg class="icon text-warning me-2" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M6,13C6.55,13 7,13.45 7,14A1,1 0 0,1 6,15A1,1 0 0,1 5,14C5,13.45 5.45,13 6,13M6,17C6.55,17 7,17.45 7,18A1,1 0 0,1 6,19A1,1 0 0,1 5,18C5,17.45 5.45,17 6,17M6,9A1,1 0 0,1 7,10A1,1 0 0,1 6,11A1,1 0 0,1 5,10A1,1 0 0,1 6,9M3,5A2,2 0 0,1 5,3H19A2,2 0 0,1 21,5V19A2,2 0 0,1 19,21H5A2,2 0 0,1 3,19V5M9,7V9H19V7H9M9,11V13H19V11H9M9,15V17H19V15H9Z" />
                                </svg>
                                نظام إدارة الصيدلية
                            </li>
                            <li class="mb-2">
                                <svg class="icon text-warning me-2" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12,2A2,2 0 0,1 14,4C14,4.74 13.6,5.39 13,5.73V7H14A7,7 0 0,1 21,14H22A1,1 0 0,1 23,15V18A1,1 0 0,1 22,19H21V20A2,2 0 0,1 19,22H5A2,2 0 0,1 3,20V19H2A1,1 0 0,1 1,18V15A1,1 0 0,1 2,14H3A7,7 0 0,1 10,7H11V5.73C10.4,5.39 10,4.74 10,4A2,2 0 0,1 12,2M7.5,13A0.5,0.5 0 0,0 7,13.5A0.5,0.5 0 0,0 7.5,14A0.5,0.5 0 0,0 8,13.5A0.5,0.5 0 0,0 7.5,13M16.5,13A0.5,0.5 0 0,0 16,13.5A0.5,0.5 0 0,0 16.5,14A0.5,0.5 0 0,0 17,13.5A0.5,0.5 0 0,0 16.5,13M13,15.5V18.5C13,19.06 12.56,19.5 12,19.5C11.44,19.5 11,19.06 11,18.5V15.5A1.5,1.5 0 0,1 12.5,14A1.5,1.5 0 0,1 14,15.5H13Z" />
                                </svg>
                                نظام التنبؤ بالأمراض (AI)
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Show success/error messages
    @if(session('success'))
    Swal.fire({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        icon: 'success',
        title: @json(session('success'))
    });
    @endif

    @if(session('error'))
    Swal.fire({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3500,
        icon: 'error',
        title: @json(session('error'))
    });
    @endif
</script>
@endpush