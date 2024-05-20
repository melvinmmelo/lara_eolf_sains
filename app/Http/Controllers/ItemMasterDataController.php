<?php

namespace App\Http\Controllers;

use App\Models\ItemMasterData;
use App\Http\Requests\StoreItemMasterDataRequest;
use App\Http\Requests\UpdateItemMasterDataRequest;

class ItemMasterDataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = ItemMasterData::branch(session('branch_code'))->get();
        return view('item-master-data', compact('products'));
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
    public function store(StoreItemMasterDataRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ItemMasterData $itemMasterData)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ItemMasterData $itemMasterData)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateItemMasterDataRequest $request, ItemMasterData $itemMasterData)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ItemMasterData $itemMasterData)
    {
        //
    }
}
