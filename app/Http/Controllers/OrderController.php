<?php
namespace App\Http\Controllers;

use App\Http\Requests\OrderCreateRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller {
    public function __construct() {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $req) {
        $user = $req->user();
        $query = Order::with(['user','items.product']);
        if (!$user->isAdmin()) $query->where('user_id', $user->id);
        $perPage = (int) $req->query('per_page', 10);
        $orders = $query->orderBy('created_at','desc')->paginate($perPage);
        return OrderResource::collection($orders)->response();
    }

    public function store(OrderCreateRequest $req) {
        $user = $req->user();
        $items = $req->input('items');

        DB::beginTransaction();
        try {
            $total = 0;
            // lock rows to avoid race conditions
            foreach ($items as $it) {
                $product = Product::lockForUpdate()->find($it['product_id']);
                if (!$product) {
                    DB::rollBack();
                    return response()->json(['message'=>'Product not found: '.$it['product_id']], 404);
                }
                if ($product->stock < $it['quantity']) {
                    DB::rollBack();
                    return response()->json(['message'=>"Insufficient stock for product {$product->id} ({$product->name})"], 422);
                }
                $total += bcmul((string)$product->price, (string)$it['quantity'], 2);
            }

            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $total,
                'status' => 'confirmed'
            ]);

            foreach ($items as $it) {
                $product = Product::find($it['product_id']);
                $product->decrement('stock', $it['quantity']);
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $it['quantity'],
                    'price' => $product->price
                ]);
            }

            DB::commit();

            $order->load(['user','items.product']);
            return (new OrderResource($order))->response()->setStatusCode(201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message'=>'Order creation failed','error'=>$e->getMessage()],500);
        }
    }

    public function show(Request $req, $id) {
        $user = $req->user();
        $order = Order::with(['user','items.product'])->find($id);
        if (!$order) return response()->json(['message'=>'Order not found'],404);
        if (!$user->isAdmin() && $order->user_id !== $user->id) return response()->json(['message'=>'Forbidden'],403);
        return new OrderResource($order);
    }

    // cancel order and rollback stock
    public function cancel(Request $req, $id) {
        $user = $req->user();
        $order = Order::with('items')->find($id);
        if (!$order) return response()->json(['message'=>'Order not found'],404);
        if (!$user->isAdmin() && $order->user_id !== $user->id) return response()->json(['message'=>'Forbidden'],403);
        if ($order->status === 'cancelled') return response()->json(['message'=>'Order already cancelled'],400);

        DB::beginTransaction();
        try {
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) $product->increment('stock', $item->quantity);
            }
            $order->status = 'cancelled';
            $order->save();
            DB::commit();
            return new OrderResource($order->load('items.product','user'));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message'=>'Cancel failed','error'=>$e->getMessage()],500);
        }
    }
}
