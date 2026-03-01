<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Response;

class CategoryController extends Controller
{
    public function __construct(protected CategoryRepository $categoryRepository) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $categories = $this->categoryRepository->findAll(
            name: $request->string('filterCategoryName'),
            isPaginated: true
        );

        return inertia('Admin/Category/CategoriesRecord', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request): void
    {
        Category::create($request->validated());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreCategoryRequest $request, Category $category): void
    {
        $category->update($request->validated());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): void
    {
        Gate::authorize('delete', $category);
        $category->delete();
    }
}
