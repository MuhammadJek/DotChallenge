<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
            $data = Post::with('author')->with('category')->orderBy('id', 'DESC')->get();
            DB::commit();
            return response()->json([
                'message' => "Data successfully returned",
                'status' => true,
                'data' => $data,
            ], 200);
            //code...
        } catch (\Throwable $th) {
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
                'title' => 'required|max:225',
                'description' => 'required',
                'author' => 'exists:users,id|integer',
                'category' => 'required|exists:categories,id|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                     'message' => 'Gagal Memasukkan data',
                       'status' => false,
                    'data' => $validator->errors(),
                 ],422);
            }


            $data = Post::create([
                'title' => $request->title,
                'description' => $request->description,
                'author' => auth()->user()->id,
                'category' => $request->category,
            ]);
            DB::commit();
            return response()->json([
                'message' => "Data successfully returned",
                'status' => true,
                'data' => $data,
            ], 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'message' => "Data fails returned",
                'status' => false,
            ], 400);
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
            $dataPost = Post::with('category')->with('author')->find($id);
            DB::commit();
            if ($dataPost) {
                return response()->json([
                    'message' => "Find Data Success",
                    'status' => true,
                    'data' => $dataPost,
                ], 200);
            }

            //code...
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
            $postData = Post::find($id);
            if (empty($postData)) {
                return response()->json([
                    'message' => "Post Not Found",
                    'status' => false,
                ], 404);
            }

            $validation = Validator::make($request->all(), [
                'title' => 'required|max:225',
                'description' => 'required',
                'author' => 'exists:users,id|integer',
                'category' => 'required|exists:categories,id|integer',
            ]);

            if ($validation->fails()) {
                return response()->json([
                     'message' => 'Gagal Memasukkan data',
                       'status' => false,
                    'data' => $validator->errors(),
                 ],422);
            }


            $postData->update([
                'title' => $request->title,
                'description' => $request->description,
                'author' => auth()->user()->id,
                'category' => $request->category,
            ]);
            DB::commit();
            return response()->json([
                'message' => "Data successfully updated",
                'status' => true,
                'data' => $postData,
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
            $postData = Post::find($id);
            if (empty($postData)) {
                return response()->json([
                    'message' => "Post Not Found",
                    'status' => false,
                ], 404);
            }
            $postData->delete();
            DB::commit();
            return response()->json([
                'message' => "Post Successfully Deleted",
                'status' => true,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => "Something went wrong",
                'status' => false,
            ], 500);
            throw $th;
        }
    }
}
