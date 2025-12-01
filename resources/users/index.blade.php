@extends('layouts.app')

@section('title', 'User Management ' . config('app.name'))

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="container mt-4" dir="ltr">
                <div class="row justify-content-center">
                    <h1 class="text-center mb-4" style="font-size: 2rem; font-weight: bold; color: #2c3e50;">Welcome to User Management</h1>
                    <div class="col-md-12">
                        @if (Session::has('Done'))
                        <div class="alert alert-success alert-dismissible fade index" role="alert">
                            {{ Session::get('Done') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        <div class="card shadow-lg border-0">
                            <div class="card-header bg-primary text-white">
                                <h4 class="d-flex justify-content-between align-items-center mb-0">
                                    User List
                                    <a href="{{ route('users.create') }}" class="btn btn-light btn-sm text-dark bg-light">Add User</a>
                                </h4>
                            </div>

                            <div class="card-body bg-light">
                                <!-- Search Bar -->
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <button onclick="printTable()" class="btn btn-outline-dark btn-sm shadow-sm">
                                        <i class="bi bi-printer"></i> Print
                                    </button>
                                    <form action="{{ route('users.index') }}" method="GET" class="d-flex w-100">
                                        <input type="text" name="search" class="form-control me-2" placeholder="Search for a user..." value="{{ request('search') }}">
                                        <button type="submit" class="btn btn-primary">Search</button>
                                    </form>
                                </div>
                                <div id="printArea">
                                    <!-- Users Table -->
                                    <div class="table-responsive">
                                        <table class="table table-hover text-center align-middle mb-0">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Role</th>
                                                    <th>Added By</th>
                                                    <th colspan="3">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($users as $user)
                                                <tr class="hover-shadow">
                                                    <td class="fw-bold">{{ $loop->iteration }}</td>
                                                    <td>{{ $user->name }}</td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>
                                                        <span class="badge {{ $user->role == 0 ? 'bg-secondary' : 'bg-success' }}">
                                                            {{ $user->role == 0 ? 'User' : 'Admin' }}
                                                        </span>
                                                    </td>

                                                    <td>{{ $user->addedBy->name ?? 'N/A' }}</td>
                                                    <td>
                                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm">
                                                            <i class="bi bi-pencil"></i> Edit
                                                        </a>
                                                    </td>
                                                    {{-- <td>
                                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">
                                                                <i class="bi bi-trash"></i> Delete
                                                            </button>
                                                        </form>
                                                    </td> --}}
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted py-4">
                                                        <i class="bi bi-info-circle"></i> No users available.
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Pagination -->
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $users->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                .hover-shadow:hover {
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                    transition: box-shadow 0.3s ease;
                }

                .card {
                    border-radius: 15px;
                }

                .card-header {
                    border-radius: 15px 15px 0 0;
                }

                .table th,
                .table td {
                    vertical-align: middle;
                }

                .badge {
                    font-size: 0.9rem;
                    padding: 0.5em 0.75em;
                }

                .btn-sm {
                    padding: 0.25rem 0.5rem;
                    font-size: 0.875rem;
                }
            </style>
            <script>
                function printTable() {
                    var printContents = document.getElementById('printArea').innerHTML;
                    var originalContents = document.body.innerHTML;

                    document.body.innerHTML = `
                            <html>
                            <head>
                                <title>Print User List</title>
                                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
                                <style>
                                    @media print {
                                        .no-print { display: none !important; }
                                        body {
                                            font-family: Arial, sans-serif;
                                            direction: ltr;
                                            text-align: center;
                                        }
                                        h3 {
                                            font-weight: bold;
                                            color: black;
                                            margin-bottom: 20px;
                                        }
                                        table {
                                            width: 100%;
                                            border-collapse: collapse;
                                            margin-top: 10px;
                                        }
                                        th, td {
                                            border: 1px solid #000;
                                            padding: 10px;
                                            text-align: center;
                                        }
                                        th {
                                            background-color: #343a40;
                                            color: white;
                                            font-size: 1.1rem;
                                            font-weight: bold;
                                        }
                                        td {
                                            font-size: 1rem;
                                            color: #333;
                                        }
                                        tr:nth-child(even) { background-color: #f8f9fa; }
                                    }
                                </style>
                            </head>
                            <body>
                                <h3>User List</h3>
                                ${printContents}
                            </body>
                            </html>
                        `;

                    window.print();
                    location.reload();
                }
            </script>
        </div>
    </div>
</div>
@endsection