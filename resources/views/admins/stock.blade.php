@extends('layouts.admin')

@section('content')
<div class="mb-4">
<button id="btnAddMaterial" class="btn btn-primary" data-url="{{ route('admin.raw-material.store') }}">
    ➕ Add New Raw Material
</button>



<div class="container-fluid mt-5">
    <div class="card shadow-sm border-0 rounded-4 w-100" style="background-color: #3e2f2f; color: #f5f5f5;">
        <div class="card-header" style="background-color: #db770c; color: #fff;">
            <h4 class="mb-0">🧾 Raw Material Stock</h4>

        </div>
        <div class="card-body">

            {{-- Stock Table --}}
            <div class="table-responsive">
                <table class="table align-middle mb-0" style="color:#f5f5f5; min-width:100%; border:1px solid #6b4c3b;">
                    <thead style="background-color: #5a3d30;" class="text-center">
                        <tr>
                            <th>#</th>
                            <th>Raw Material</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Status</th>
                            <th>Action</th>

                        </tr>
                    </thead>
                    <tbody class="text-center">
                      @foreach($rawMaterials as $material)
<tr>
    <td>{{ $material->id }}</td>
    <td>{{ $material->name }}</td>
@php
    $displayQty = $material->quantity;
    $displayUnit = $material->unit;

    if ($displayUnit == 'g' && $displayQty >= 1000) {
        $displayQty = $displayQty / 1000;
        $displayUnit = 'kg';
    }

    if ($displayUnit == 'ml' && $displayQty >= 1000) {
        $displayQty = $displayQty / 1000;
        $displayUnit = 'L';
    }
@endphp

<td>{{ number_format($displayQty, 2) }}</td>
<td>{{ $displayUnit }}</td>

    <td>
        <span class="badge {{ $material->quantity < 5 ? 'bg-danger' : 'bg-success' }}">
            {{ $material->quantity < 5 ? 'Low' : 'OK' }}
        </span>
    </td>
    <td>
<form action="{{ route('admin.raw-material.update', $material->id) }}" method="POST">
    @csrf
    @method('PATCH')
    <input
        type="text"
        name="quantity"
        value="{{ $material->quantity }}"
        inputmode="decimal"
        class="form-control"
        style="width:80px; display:inline-block;"
    >
    <button type="submit" class="btn btn-sm btn-primary">Update</button>
</form>

    <!-- Delete button only if quantity = 0 -->
        <form action="{{ route('admin.raw-material.destroy', $material->id) }}" method="POST" style="display:inline-block;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this material?');">Delete
            </button>
        </form>
    </td>
</tr>
@endforeach   </tbody>
                </table>
            </div>

            <a href="{{ route('admins.dashboard') }}" class="btn btn-light mt-3" style="color:#3e2f2f;">
                <i class="bi bi-arrow-left-circle"></i> Back to Dashboard
            </a>

        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(Session::has('success'))
<script>
Swal.fire({
  icon: 'success',
  title: 'Success!',
  text: '{{ Session::get('success') }}',
  confirmButtonColor: '#db770c'
});
</script>
@endif
<script src="{{ asset('assets/js/raw-material.js') }}"></script>

{{-- ✅ SweetAlert Library --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- ✅ Custom JS File --}}
<script src="{{ asset('assets/js/raw-material.js') }}"></script>

{{-- ✅ Success Toast --}}
@if(Session::has('success'))
<script>
Swal.fire({
  icon: 'success',
  title: 'Success!',
  text: '{{ Session::get('success') }}',
  confirmButtonColor: '#db770c'
});
</script>
@endif


@endsection
