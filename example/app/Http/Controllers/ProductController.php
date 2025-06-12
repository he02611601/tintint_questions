<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = DB::select("SELECT * FROM products");
        return $products;
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
        // 未使用
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // 未使用
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
        // 未使用
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // 未使用
    }
}
