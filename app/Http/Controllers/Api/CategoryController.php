<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $per_page = $request->input('per_page');
        $sortBy = $request->input('sortBy', 'id');
        $sortDesc = $request->input('sortDesc', 'asc');
        $search = $request->input('search');
        $category_id = $request->input('category_id');

        $categories = Category::select('categories.*', 'main_categories.name as main_category_name')
            ->leftJoin('categories as main_categories', 'main_categories.id', 'categories.category_id');

        if ($search) {
            $categories->where(function ($query) use ($search) {
                $query->where('categories.name', 'like', "%$search%");
                $query->where('main_categories.name', 'like', "%$search%");
            });
        }

        $categories->orderBy($sortBy, $sortDesc);

        if ($per_page) {
            $categories = $categories->paginate($per_page);
        } else {
            $categories = $categories->get();
        }
        return response()->json([
            'status' => true,
            'categories' => $categories,
            'param' => $request->all()
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation fail',
                'errors' => $validator->errors()
            ], 422);
        }

        $category_id = $request->input('category_id');

        $main_category = Category::find($category_id);

        if ($category_id && !$main_category) {
            return response()->json([
                'status' => false,
                'message' => 'Not found Category ID = ' . $category_id . ' in our system',
            ], 422);
        }

        if ($main_category && $main_category->level == 3) {
            return response()->json([
                'status' => false,
                'message' => 'You can have 3 level category',
            ], 422);
        }

        try {
            $category = Category::create([
                'category_id' => $request->input('category_id'),
                'name' => $request->input('name'),
                'level' => $main_category ? $main_category->level + 1 : 1
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Create category success',
                'category' => $category
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json([
            'status' => false,
            'category' => Category::find($id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation fail',
                'errors' => $validator->errors()
            ], 422);
        }

        $category_id = $request->input('category_id');

        $main_category = Category::find($category_id);

        if ($category_id == $id) {
            return response()->json([
                'status' => false,
                'message' => 'id and category_id cannot be the same',
            ], 422);
        }

        if ($category_id && !$main_category) {
            return response()->json([
                'status' => false,
                'message' => 'Not found Category ID = ' . $category_id . ' in our system',
            ], 422);
        }

        if ($main_category && $main_category->level == 3) {
            return response()->json([
                'status' => false,
                'message' => 'You can have 3 level category',
            ], 422);
        }

        try {
            $old_category = Category::find($id);
            $level = 1;
            if ($category_id && $old_category->category_id == $category_id) {
                $level = $old_category->level;
            } elseif ($category_id && $old_category->category_id != $main_category->id) {
                $level = $main_category->level + 1;
            }
            $category = Category::where('id', $id)->update([
                'category_id' => $request->input('category_id'),
                'name' => $request->input('name'),
                'level' => $level
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Update category success',
                'category' => $category
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Category::find($id)->delete();
        return response()->json([
            'status' => true,
            'message' => 'Delete category',
        ]);
    }
}
