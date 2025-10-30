@extends('layouts.admin')

@section('content')

<div class="container mt-5">
    <div class="card shadow-sm border-0 rounded-4" style="background-color: #3e2f2f; color: #f5f5f5;">
        <div class="card-header" style="background-color: #db770cff; color: #fff;">
            <h4 class="mb-0 d-flex justify-content-between align-items-center">
                Admins List
                <a href="{{ route('create.admins') }}" class="btn btn-light btn-sm" style="color:#3e2f2f;">
                    <i class="bi bi-person-plus"></i> Add Admin
                </a>
            </h4>
        </div>
        <div class="card-body">

            {{-- Flash messages --}}
            @if (Session::has('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm mt-3" role="alert">
                    {{ Session::get('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Admins Table --}}
            <div class="table-responsive mt-3">
                <table class="table table-bordered align-middle text-white" style="border-color: #6b4c3b;">
                    <thead style="background-color: #5a3d30;">
                        <tr class="text-center">
                            <th>No.</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($allAdmins as $admin)
                            <tr class="text-center" style="border-bottom:1px solid #6b4c3b;">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $admin->name }}</td>
                                <td>{{ $admin->email }}</td>
                                <td>••••••••</td>
                                <td>
                                    <a href="{{ route('edit.admin', $admin->id) }}" class="btn btn-warning btn-sm me-1">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <form action="{{ route('delete.admin', $admin->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this admin?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No admins found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Back Button --}}
            <a href="{{ route('admins.dashboard') }}" class="btn btn-light mt-3" style="color:#3e2f2f;">
                <i class="bi bi-arrow-left-circle"></i> Back to Dashboard
            </a>
        </div>
    </div>
</div>

{{-- Bootstrap Icons --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endsection
