<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponseTrait;
use App\Models\feedback;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FeedbackController extends Controller
{
    use ApiResponseTrait;
    public function index()
    {
        //
        $feedback=feedback::with('course','student')->get();
        // $feedback = feedback::get();
        return response()->json($feedback, 200);
    }

    public function store(Request $request)
    {
        $validation = $this->validation($request);
        if ($validation instanceof Response) {
            return $validation;
        }
        if (is_null($validation)) {
            $feedback = feedback::create([
                'name' => $request->name,
                'desc' => $request->desc,
                'student_id' => $request->student_id,
                'course_id' => $request->course_id
            ]);

            if ($feedback) {
                // return $this->createdResponse($contact_us);
                return response()->json($feedback, 200);
            }
        }
        // $this->unKnowError();
        return response()->json("Cannot send this feedback", 400);
    }


    public function show($id)
    {
        $feedback = feedback::with(['course','student'])->find($id);
        if ($feedback) {
            // $feedback->course;
            // $feedback->student;
            // return $this->apiResponse($course);
            return response()->json($feedback, 200);
        }
        // return $this->notFoundResponse();
        return response()->json("Not Found", 404);
    }


    public function update(Request $request,  $id)
    {
        //
        $feedback = feedback::find($id);
        if ($feedback) {
            if ($request->isMethod('put')) {
                $validation = $this->validation($request);
                if ($validation instanceof Response) {
                    return $validation;
                }
            }

            $feedback->update($request->all());
            return response()->json($feedback, 200);
        }
        return response()->json("Not found", 404);
    }

   
    public function destroy($id)
    {
        $feedback = feedback::find($id);
        if (is_null($feedback)) {
            return response()->json("Record not found", 404);
        }
        $feedback->delete();
        return response()->json(null, 204);
    }


    public function validation($request)
    {
        return $this->apiValidation($request, [
            'name' => 'required|min:3',
            'course_id' => 'exists:App\Models\Course,id',
            'student_id' => 'exists:App\Models\Student,id',
            'desc' => 'required|min:6|max:200'
        ]);
    }
}
