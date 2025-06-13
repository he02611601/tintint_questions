<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 分頁
        $offset = $request->page == 1 ? 0 : ($request->page - 1) * $request->pageSize;
        $limit = $request->pageSize == 0 ? 10 : $request->pageSize;

        // 取資料
        $orders = DB::select("SELECT * FROM orders ORDER BY id LIMIT ? OFFSET ?", [$limit, $offset]);
        return response($orders, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // 未使用
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                // 計算訂單總額
                $totalAmount = 0;
                foreach ($request->items as $item) {
                    $product = DB::select("SELECT * FROM products WHERE id = ?", [$item['product_id']]);
                    if ($product[0]->stock < $item['quantity']) {
                        throw new Exception('庫存不足');
                    }
                    $totalAmount += $product[0]->price * $item['quantity'];
                }
    
                // 新增訂單
                $orderCount = DB::select("SELECT COUNT(*) AS count FROM orders");
                $orderNumber = 'ORD' . str_pad($orderCount[0]->count + 1, 6, '0', STR_PAD_LEFT);
                DB::insert("INSERT INTO orders (user_id, order_number, status, total_amount) VALUES (?, ?, ?, ?)", [$request->user_id, $orderNumber, 'pending', $totalAmount]);
    
                // 新增訂單項目
                $orderItems = [];
                foreach ($request->items as $item) {
                    $product = DB::select("SELECT * FROM products WHERE id = ?", [$item['product_id']]);
                    DB::update("UPDATE products SET stock = stock - ? WHERE id = ?", [$item['quantity'], $item['product_id']]);
                    $orderItems[] = [
                        'order_id' => $orderCount[0]->count + 1,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $product[0]->price,
                        'subtotal' => $product[0]->price * $item['quantity'],
                    ];
                }
                DB::table('order_items')->insert($orderItems);
            });
    
        } catch (Exception $e) {
            return response('新增訂單失敗', 400);
        }
        
        return response('新增訂單成功', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // 取資料
        $order = DB::select("SELECT * FROM orders ORDER BY id WHERE id = ?", [$id]);
        return response($order[0] ?? null, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // 未使用
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // 檢查狀態
        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($request->status, $statuses)) {
            return response('更新狀態不符', 400);
        }

        // 檢查訂單是否存在
        $order = DB::select('SELECT * FROM orders ORDER BY id WHERE id = ?', [$id]);
        if (count($order) < 0) {
            return response('資料不存在', 404);
        }

        // 更新狀態
        $order = DB::update("UPDATE orders SET status = ? WHERE id = ?", [$request->status, $id]);
        return response('更新成功', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // 未使用
    }

    public function stats()
    {
        // 總計
        $totalStats = DB::select("SELECT COUNT(*) AS count, SUM(total_amount) AS sum FROM orders");
        // 今日(按照提供的假資料對應今日為2025-06-05)，如果這邊的今日是指實際的今日那語法會在做調整
        $todayStats = DB::select("SELECT COUNT(*) AS count, SUM(total_amount) AS sum FROM orders WHERE created_at >= '2025-06-05 00:00:00' AND created_at < '2025-06-06 00:00:00'");
        return response([
            'totalOrder' => $totalStats[0]->count,
            'totalAmount' => is_null($totalStats[0]->sum) ? 0 : intval($totalStats[0]->sum),
            'todayOrder' => $todayStats[0]->count,
            'todayAmount' => is_null($todayStats[0]->sum) ? 0 : intval($todayStats[0]->sum),
        ], 200);
    }
}
