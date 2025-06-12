<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $offset = $request->page == 1 ? 0 : ($request->page - 1) * $request->pageSize;
        $limit = $request->pageSize == 0 ? 10 : $request->pageSize;

        $orders = DB::select("SELECT * FROM orders LIMIT ? OFFSET ?", [$limit, $offset]);
        return $orders;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       // 未使用
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = DB::select("SELECT * FROM orders WHERE id = ?", [$id]);
        return $order[0] ?? null;
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
        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($request->status, $statuses)) {
            return false;
        }

        $order = DB::select('SELECT * FROM orders WHERE id = ?', [$id]);
        if (count($order) < 0) {
            return false;
        }

        $order = DB::update("UPDATE orders SET status = ? WHERE id = ?", [$request->status, $id]);
        return true; 
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
        return [
            'totalOrder' => $totalStats[0]->count,
            'totalAmount' => is_null($totalStats[0]->sum) ? 0 : intval($totalStats[0]->sum),
            'todayOrder' => $todayStats[0]->count,
            'todayAmount' => is_null($todayStats[0]->sum) ? 0 : intval($todayStats[0]->sum),
        ];
    }
}
