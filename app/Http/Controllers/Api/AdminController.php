<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Hash;
use Validator;

class AdminController extends Controller
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

        $admins = Admin::select('admins.*', 'admin_groups.name as group_name')
            ->leftJoin('admin_groups', 'admin_groups.id', 'admins.admin_group_id');

        if ($search) {
            $admins->where(function ($query) use ($search) {
                $query->where('admins.name', 'like', "%$search%");
                $query->orWhere('admins.email', 'like', "%$search%");
                $query->orWhere('admins.phone', 'like', "%$search%");
                $query->orWhere('admin_groups.name', 'like', "%$search%");
            });
        }

        $admins->orderBy($sortBy, $sortDesc);

        if ($per_page) {
            $admins = $admins->paginate($per_page);
        } else {
            $admins = $admins->get();
        }
        return response()->json([
            'status' => true,
            'admins' => $admins,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'email' => 'required|email|unique:admins',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation fail',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $admin = Admin::create([
                'admin_group_id' => $request->input('admin_group_id'),
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'password' => Hash::make($request->input('password')),
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Create Admin success',
                'admin' => $admin
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
        $admin = Admin::select('id', 'admin_group_id', 'name', 'email', 'phone', 'created_at', 'updated_at')
            ->whereId($id)
            ->first();
        return response()->json([
            'status' => false,
            'admin' => $admin
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
            'email' => ['required', 'email', Rule::unique('admins')->ignore($id)],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation fail',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $admin = Admin::where('id', $id)->update([
                'admin_group_id' => $request->input('admin_group_id'),
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
            ]);

            $password = $request->input('password');
            if ($password) {
                $admin->password = Hash::make($password);
                $admin->save();
            }

            return response()->json([
                'status' => true,
                'message' => 'Update Admin success',
                'admin' => $admin
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
        Admin::find($id)->delete();
        return response()->json([
            'status' => true,
            'message' => 'Delete Admin Group',
        ]);
    }
}
