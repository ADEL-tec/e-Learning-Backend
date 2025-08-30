<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{

    // return all the course list
    public function courseList()
    {

        try {
            $result = Course::select('name', 'thumbnail', 'lesson_num', 'price', 'id')->get();
            return response()->json([
                'code' => 200,
                'msg' => 'my course list is here',
                "data" => $result,
            ], 200);
            return $response;
        } catch (\Throwable $throw) {
            return response()->json([
                'code' => 500,
                'msg' => $throw->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function courseDetail(Request $request)
    {
        $id = $request->id;
        try {
            $result = Course::where('id', '=', $id)->select(
                'id',
                'name',
                'user_token',
                'description',
                'video_length',
                'thumbnail',
                'lesson_num',
                'price',
                'downloadable_res',

            )->first();
            return response()->json([
                'code' => 200,
                'msg' => 'my course detil is here',
                "data" => $result,
            ], 200);
            return $response;
        } catch (\Throwable $throw) {
            return response()->json([
                'code' => 500,
                'msg' => $throw->getMessage(),
                'data' => null,
            ], 500);
        }
    }
}
