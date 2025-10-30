@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <h5>Add New Raw Material</h5>
    <form action="{{ route('admin.raw-material.store') }}" method="POST" class="row g-2">
        @csrf
        <div class="col-md-5">
            <input type="text" name="name" class="form-control" placeholder="Raw Material Name" required>
        </div>
        <div class="col-md-3">
            <input type="number" name="quantity" class="form-control" placeholder="Quantity" min="0" required>
        </div>
<div class="col-md-2">
    <select name="unit" class="form-control" required>
    <option value="g">Gram (g)</option>
    <option value="kg">Kilogram (kg)</option>
    <option value="ml">Milliliter (ml)</option>
    <option value="l">Liter (L)</option>
    <option value="pcs">Pieces (pcs)</option>
</select>
</div>


        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Add</button>
        </div>
    </form>
</div>

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
            <input type="number" name="quantity" value="{{ $material->quantity }}" min="0" class="form-control" style="width:80px; display:inline-block;">
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
@endsection
