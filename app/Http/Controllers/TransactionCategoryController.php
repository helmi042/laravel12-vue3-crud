<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionCategoryRequest;
use App\Http\Requests\UpdateTransactionCategoryRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TransactionCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = $request->user()
            ->transactionCategories()
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return Inertia::render('transaction-categories/Index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('transaction-categories/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionCategoryRequest $request)
    {
        $request->user()->transactionCategories()->create($request->validated());

        return redirect()->route('transaction-categories.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $transactionCategory)
    {
        $category = $request->user()
            ->transactionCategories()
            ->findOrFail($transactionCategory);

        return Inertia::render('transaction-categories/Show', [
            'category' => $category,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $transactionCategory)
    {
        $category = $request->user()
            ->transactionCategories()
            ->findOrFail($transactionCategory);

        return Inertia::render('transaction-categories/Edit', [
            'category' => $category,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionCategoryRequest $request, $transactionCategory)
    {
        $category = $request->user()
            ->transactionCategories()
            ->findOrFail($transactionCategory);

        $category->update($request->validated());

        return redirect()->route('transaction-categories.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $transactionCategory)
    {
        $category = $request->user()
            ->transactionCategories()
            ->findOrFail($transactionCategory);

        $category->delete();

        return redirect()->route('transaction-categories.index');
    }
}
