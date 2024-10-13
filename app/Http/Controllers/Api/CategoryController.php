<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
    public function index()
    {
        /****
         * 
         * Display index information
         * 
         */
        try {
            DB::beginTransaction();
            $dataCategory = Category::all();
            DB::commit();
            if ($dataCategory) {
                return response()->json([
                    'message' => 'List Category successfully returned',
                    'status' => true,
                    'data' => $dataCategory
                ], 200);
            }
            return response()->json([
                'message' => 'List Category not found',
                'status' => false,
            ], 404);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Something went wrong',
                'status' => false,
            ], 500);
            throw $th;
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /**
         * form for store a new resource.
         *
         * @return \Illuminate\Http\Response
         */
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'name' => 'required|max:225',
                'description' => 'required',
            ]);

             if ($validator->fails()) {
                return response()->json([
                     'message' => 'Gagal Memasukkan data',
                       'status' => false,
                    'data' => $validator->errors(),
                 ],422);
            }


            $dataCategory = Category::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);
            DB::commit();
            if ($dataCategory) {
                return response()->json([
                    'message' => "Data successfully returned",
                    'status' => true,
                    'data' => $dataCategory,
                ], 200);
            }
            return response()->json([
                'message' => "Data fails returned",
                'status' => false,
            ], 400);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'message' => "Something Wrong Serve",
                'status' => false,
            ], 500);
            // throw $th;
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
        try {
            DB::beginTransaction();
            $dataCategory = Category::find($id);
            DB::commit();
            if ($dataCategory) {
                return response()->json([
                    'message' => "Find Data Success",
                    'status' => true,
                    'data' => $dataCategory,
                ], 200);
            }
            //code..
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return response()->json([
                'message' => "Not Find Data Success",
                'status' => false,
            ], 400);
        }
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

        try {
            DB::beginTransaction();
            $dataCategory = Category::find($id);
            if (empty($dataCategory)) {
                return response()->json([
                    'message' => "Category Not Found",
                    'status' => false,
                ], 404);
            }

            $validation = Validator::make($request->all(), [
                'name' => 'required|max:225',
                'description' => 'required',
            ]);

            if ($validation->fails()) {
                return response()->json([$validation->messages()]);
            }


            $dataCategory->update([
                'name' => $request->name,
                'description' => $request->description,

            ]);
            DB::commit();
            return response()->json([
                'message' => "Category successfully updated",
                'status' => true,
                'data' => $dataCategory,
            ], 200);
        } catch (\Throwable $th) {
            throw $th;
        }
        //
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            //code...
            DB::beginTransaction();
            $dataCategory = Category::find($id);
            $dataPost = Post::where('category', $dataCategory->id)->first();


            if (empty($dataCategory)) {
                return response()->json([
                    'message' => "Category Not Found",
                    'status' => false,
                ], 404);
            }
            if (!empty($dataPost)) {
                return response()->json([
                    'message' => "Category have use Post " . $dataPost->title,
                    'status' => false,
                    'data' => $dataPost
                ], 402);
            }
            $dataCategory->delete();
            DB::commit();
            return response()->json([
                'message' => "Category Successfully Deleted",
                'status' => true,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => "Something went wrong",
                'status' => false,
            ], 500);
            throw $th;
        }
    }
}
