<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminGroup;
use Validator;

class AdminGroupController extends Controller
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

        $admin_groups = AdminGroup::select('admin_groups.*');

        if ($search) {
            $admin_groups->where(function ($query) use ($search) {
                $query->where('admin_groups.name', 'like', "%$search%");
            });
        }

        $admin_groups->orderBy($sortBy, $sortDesc);

        if ($per_page) {
            $admin_groups = $admin_groups->paginate($per_page);
        } else {
            $admin_groups = $admin_groups->get();
        }
        return response()->json([
            'status' => true,
            'admin_groups' => $admin_groups,
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

        try {
            $admin_group = AdminGroup::create([
                'name' => $request->input('name'),
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Create Admin Group success',
                'admin_group' => $admin_group
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
            'admin_group' => AdminGroup::find($id)
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

        try {
            $admin_group = AdminGroup::where('id', $id)->update([
                'name' => $request->input('name'),
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Create Admin Group success',
                'admin_group' => $admin_group
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
        AdminGroup::find($id)->delete();
        return response()->json([
            'status' => true,
            'message' => 'Delete Admin Group',
        ]);
    }
}
