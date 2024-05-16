<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function listProducts()
    {
        //
        $products = Product::paginate(50);
        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    
    public function createProduct(Request $request)
    {
        //
        Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
        ])->validated();
        
        $product = new Product();
        $product->name = $request->name;
        $product->description = $request->description;
        $product->cost = $request->cost ?  $request->cost : 0;
        $product->price = $request->price ?  $request->price : 0;
        $product->save();

        return response()->json($product);
        
    }
    public function createNewOrder(Request $request)
    {
        $user = User::find($request->user_id);
        $order_code = str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);
        // $product = Product::find($request->product_id);

        foreach ($request->details as $detail) {
            $user->products()->attach([
                $detail['product_id'] => [
                    'order_code' => $order_code,
                    'quantity' => $detail['quantity']
                ]
            ]);
        }
        return response()->json(['message' => 'success']);
    }
    /**
     * Display the specified resource.
     */
    public function getUsersByProduct($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $users = $product->users;

        return response()->json(['users' => $users]);
    }

    public function getProductsByOrder($orderCode)
    {
        $products = Product::whereHas('users', function ($query) use ($orderCode) {
            $query->where('order_code', $orderCode);
        })->get();

        if ($products->isEmpty()) {
            return response()->json(['message' => 'No products found for this order'], 404);
        }

        return response()->json(['products' => $products]);
    }
    public function getAllOrders()
    {
        $orders = DB::table('product_user')
            ->select('order_code', DB::raw('group_concat(product_id) as products'), DB::raw('group_concat(quantity) as quantities'))
            ->groupBy('order_code')
            ->get();

        return response()->json(['orders' => $orders]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function updateProduct(Request $request, $product_id)
    {
        $product = Product::find($product_id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->update($request->all());

        return response()->json(['message' => 'Product updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteProduct($product_id)
    {
        $product = Product::find($product_id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Verifica si el producto tiene pedidos asociados
        $orders = $product->users()->wherePivot('order_code', '!=', null)->count();

        if ($orders > 0) {
            return response()->json(['message' => 'Cannot delete product. It has associated orders.'], 400);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
