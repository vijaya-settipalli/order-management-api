<?php
namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller {
    public function __construct() {
        $this->middleware('auth:sanctum')->except(['index','show']);
    }

    public function index(Request $req) {
        $query = Product::query();

        if ($q = $req->query('q')) {
            $query->where('name','like',"%{$q}%")
                  ->orWhere('sku','like',"%{$q}%");
        }
        if ($min = $req->query('min_price')) $query->where('price','>=',$min);
        if ($max = $req->query('max_price')) $query->where('price','<=',$max);

        $perPage = (int) $req->query('per_page', 10);
        $products = $query->orderBy('created_at','desc')->paginate($perPage);
        return ProductResource::collection($products)->response();
    }

    public function store(ProductRequest $req) {
        if (Gate::denies('admin-actions')) {
            return response()->json(['message'=>'Forbidden'],403);
        }
        $product = Product::create($req->validated());
        return new ProductResource($product);
    }

    public function show($id) {
        $product = Product::find($id);
        if (!$product) return response()->json(['message'=>'Product not found'],404);
        return new ProductResource($product);
    }

    public function update(ProductRequest $req, $id) {
        if (Gate::denies('admin-actions')) {
            return response()->json(['message'=>'Forbidden'],403);
        }
        $product = Product::find($id);
        if (!$product) return response()->json(['message'=>'Product not found'],404);
        $product->update($req->validated());
        return new ProductResource($product);
    }

    public function destroy($id) {
        if (Gate::denies('admin-actions')) {
            return response()->json(['message'=>'Forbidden'],403);
        }
        $product = Product::find($id);
        if (!$product) return response()->json(['message'=>'Product not found'],404);
        $product->delete();
        return response()->json(['message'=>'Product deleted'],200);
    }
}
