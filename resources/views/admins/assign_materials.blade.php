@extends('layouts.admin')

@section('content')
<h5>Assign Raw Materials to Product: {{ $product->name }}</h5>

<form action="{{ route('admin.product.addMaterials', $product->id) }}" method="POST">
    @csrf
    <table class="table">
        <thead>
            <tr>
                <th>Raw Material</th>
                <th>Quantity Required</th>
                <th>Unit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rawMaterials as $material)
            <tr>
                <td>{{ $material->name }}</td>
                <td>
                    <input type="number" name="materials[{{ $material->id }}]"
                           value="{{ old('materials.'.$material->id, 0) }}" step="0.01" min="0">
                </td>
                <td>{{ $material->unit }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <button type="submit" class="btn btn-primary">Save Recipe</button>
</form>

<a href="{{ route('admins.dashboard') }}" class="btn btn-light mt-3" style="color:#3e2f2f;">
    <i class="bi bi-arrow-left-circle"></i> Back to Dashboard
</a>
@endsection
