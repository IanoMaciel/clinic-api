<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Ramsey\Uuid\Type\Integer;

class CategoryController extends Controller {

    protected $category;

    public function __construct(Category $category) {
        $this->category = $category;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse {
        try {
            $categories = $this->category->query()->get();
            return response()->json($categories, 200);
        } catch (\Exception $error) {
            return response()->json([
                'message' => 'Error processing request',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse {
        $request->validate($this->category->rules(), $this->category->feedback());
        try {
            $category = $this->category->query()->create($request->all());
            return response()->json($category, 201);
        } catch (\Exception $error) {
            return response()->json([
                'message' => 'Error processing request',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse {
        $request->validate($this->category->rules(), $this->category->feedback());

        $category = $this->category->query()->find($id);
        if (!$category) return response()->json(['message' => 'Category not found'], 404);

        try {
            $category->update($request->all());
            return response()->json($category, 201);
        } catch (\Exception $error) {
            return response()->json([
                'message' => 'Error processing request',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        //
    }
}
