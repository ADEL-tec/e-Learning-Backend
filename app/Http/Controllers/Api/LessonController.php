<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function lessonList(Request $request)
    {
        $courseId = $request->id;
        $result = Lesson::where('course_id', '=', $courseId)
            ->select('id', 'name', 'thumbnail', 'description', 'video')
            ->get();
        try {
            return response()->json([
                'code' => 200,
                'msg' => 'success',
                'data' => $result
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'code' => 200,
                'msg' => 'internal server error',
                'data' => $e->getMessage()
            ], 500);
        }
    }
    public function lessonDetail(Request $request)
    {
        $lessonId = $request->id;
        $result = Lesson::where('id', '=', $lessonId)
            ->select('id', 'video')
            ->first();
        try {
            return response()->json([
                'code' => 200,
                'msg' => 'success',
                'data' => $result
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'code' => 200,
                'msg' => 'internal server error',
                'data' => $e->getMessage()
            ], 500);
        }
    }
}
