<?php
namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product\Product;
use App\Models\RawMaterial;
use App\Models\Product\Order;


class RawMaterialController extends Controller
{
    // Show the form to create a new raw material
    public function create()
    {
        return view('admins.create_raw_material'); // create this Blade view
    }

    // Store the new raw material in the database
        public function store(Request $request)
{
    $request->validate([
        'name' => 'required|max:100|unique:raw_materials,name',
        'quantity' => 'required|numeric|min:0',
        'unit' => 'required|string|max:10',
    ]);

    // Convert to base units
    $quantity = $request->quantity;
    $unit     = $request->unit;

    switch ($unit) {
        case 'kg':
            $quantity = $quantity * 1000;
            $unit = 'g';
            break;

        case 'g':
            $unit = 'g';
            break;

        case 'l':
            $quantity = $quantity * 1000;
            $unit = 'ml';
            break;

        case 'ml':
            $unit = 'ml';
            break;

        default:
            $unit = 'pcs';
    }

    RawMaterial::create([
        'name' => $request->name,
        'quantity' => $quantity, // ✅ use converted
        'unit' => $unit,         // ✅ use converted
    ]);

    return redirect()->route('admin.raw-material.stock')->with('success', 'Raw material added successfully!');
}


    // Show all raw materials
    public function index()
    {
        $rawMaterials = RawMaterial::orderBy('id', 'asc')->get();
        return view('admins.stock', compact('rawMaterials'));
    }

    // Update raw material quantity
    public function update(Request $request, $id)
    {
        $request->validate([
           'quantity' => 'required|numeric|min:0',
        ]);

        $material = RawMaterial::findOrFail($id);
        $material->quantity = $request->quantity;
        $material->save();

        return redirect()->route('admin.raw-material.stock')->with('success', 'Stock updated successfully!');
    }

    // Place an order using raw materials
    public function orderProduct(Request $request)
    {
        $product = Product::findOrFail($request->product_id);
        $quantity = $request->quantity;

        foreach ($product->rawMaterials as $material) {
            if ($material->quantity < ($material->pivot->quantity_required * $quantity)) {
                return back()->with('error', $material->name . ' is not enough!');
            }
        }

        foreach ($product->rawMaterials as $material) {
            $material->quantity -= $material->pivot->quantity_required * $quantity;
            $material->save();
        }

        Order::create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'price' => $product->price * $quantity,
            'status' => 'Pending'
        ]);

        return back()->with('success', 'Order placed and stock updated!');
    }

// Show raw material stock
public function viewRawMaterials()
{
    $rawMaterials = \App\Models\RawMaterial::orderBy('id', 'asc')->get();
    return view('admins.stock', compact('rawMaterials'));
}



// Update raw material quantity
public function updateRawMaterial(Request $request, $id)
{
    $request->validate([
        'quantity' => 'required|integer|min:0',
    ]);

    $material = RawMaterial::findOrFail($id);
    $material->quantity = $request->quantity;
    $material->save();

    return redirect()->route('admin.raw-material.stock')->with('success', 'Stock updated successfully!');
}
public function destroy($id)
{
    $material = RawMaterial::findOrFail($id);

    if ($material->quantity > 0) {
        return redirect()->back()->with('delete', 'Cannot delete a material that still has stock!');
    }

    $material->delete();

    return redirect()->back()->with('success', 'Material deleted successfully!');
}


}
