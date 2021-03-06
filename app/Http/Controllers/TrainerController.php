<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponseTrait;
use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class TrainerController extends Controller
{
    use ApiResponseTrait;
    public function index()
    {
        //
        $trainers = Trainer::get();
        return $this->apiResponse($trainers);
    }

    public function store(Request $request)
    {
        //
        $validation = $this->validation($request);
        if($validation instanceof Response){
            return $validation;
        }

        $img=$request->file('img');             //bmsek el soura
        $ext=$img->getClientOriginalExtension();   //bgeb extention
        $image="train -".uniqid().".$ext";            // conncat ext +name elgded
        $img->move(public_path("uploads/trainer/"),$image);


        $trainers = Trainer::create([
            'fname'=>$request->fname ,
            'lname'=>$request->lname ,
            'gender'=>$request->gender ,
            'phone'=>$request->phone ,
            'img'=>$image,
            'email'=>$request->email ,
            'password'=>Hash::make($request->password),
            'facebook'=>$request->facebook ,
            'twitter'=>$request->twitter ,
            'linkedin'=> $request->linkedin ,
        ]);
        if ($trainers) {
            return $this->createdResponse($trainers);
        }

        $this->unKnowError();
    }

    public function register(Request $request)
    {
        $validation = $this->validation($request);
        if($validation instanceof Response){
            return $validation;
        }

        $trainers = Trainer::create([
            'fname'=>$request->fname ,
            'lname'=>$request->lname ,
            'gender'=>$request->gender ,
            'phone'=>$request->phone ,
            'email'=>$request->email ,
            'password'=>Hash::make($request->password),

        ]);
        if ($trainers) {
            return $this->createdResponse($trainers);
        }

        $this->unKnowError();
    }


    public function show($id)
    {
        $trainer = Trainer::find($id);
        if ($trainer) {
            return $this->apiResponse($trainer);
        }
        return $this->notFoundResponse();
    }
    public function update(Request $request,$id)
    {
        $validation=$this->apiValidation($request , [
                'fname' => 'required|min:3|max:20',
                'lname' => 'required|min:3|max:20',
                'phone' => 'required|min:10',
                'img' => 'image|mimes:jpeg,png',
                // 'facebook' => 'required',
                // 'twitter' => 'required',
                // 'linkedin' => 'required',
            ]);
        //$validation = $this->validation($request);
        if($validation instanceof Response){
            return $validation;
        }

        $trainer = Trainer::find($id);
        if (!$trainer) {
            return $this->notFoundResponse();
        }


        $name=$trainer->img;
        // Log::alert($name !== null);
        if ($request->hasFile('img'))
        {
            if($name !== null)
            {
                $path_parts = pathinfo(basename($name));
                
                Cloudinary::destroy($path_parts['filename']);
            }
            //move
        // $img=$request->file('img');             //bmsek el soura
        // $ext=$img->getClientOriginalExtension();   //bgeb extention
        // $name="train -".uniqid().".$ext";            // conncat ext +name elgded
        // $img->move(public_path("uploads/trainer/"),$name);   //elmkan , $name elgded
        $name = Cloudinary::upload($request->file('img')->getRealPath())->getSecurePath();

        }

        $trainer->update([
            'fname'=>$request->fname ,
            'lname'=>$request->lname ,
            // 'gender'=>$trainer->gender ,
            'phone'=>$request->phone ,
            'img'=>$name,
            // 'email'=>$trainer->email ,
            // 'password'=>Hash::make($trainer->password),
            'facebook'=>$request->facebook ,
            'twitter'=>$request->twitter ,
            'linkedin'=> $request->linkedin ,
        ]);

        if ($trainer) {
            return $this->createdResponse($trainer);
        }

        $this->unKnowError();

    }

    public function destroy($id)
    {
        $trainer = Trainer::find($id);
        if ($trainer) {
            $img_name = $trainer->img;
    
            if ($img_name !== null) {
               
            $path_parts = pathinfo(basename($img_name));
                    
              Cloudinary::destroy($path_parts['filename']);
            }



            $trainer->delete();
            return $this->deleteResponse();
        }
        return $this->notFoundResponse();
    }

    public function getCount()
    {
        $data = DB::table('trainers')->select('id')->count('id');
        if ($data == 0)
            return response()->json($data, 200);
        if ($data) {
            return response()->json($data, 200);
        }
        return response()->json("Not Found", 404);
    }

    public function validation($request){
        return $this->apiValidation($request , [
            'fname' => 'required|min:3|max:20',
            'lname' => 'required|min:3|max:20',
            'gender' => 'required',
            'phone' => 'required|unique:trainers',
            'email' => 'required|email|unique:trainers',
            'password' => 'required|min:6'

        ]);
    }

    public function login(Request $request)
    {
        // Log::alert($request);
        $validator = $this->apiValidation($request , [
            'email' => 'required|exists:trainers,email' ,
            'password' => 'required|string' ,
        ]);

        if($validator instanceof Response){
            return $validator;
        }

        $credentials = request(['email', 'password']);
        // dd($credentials);
        if (!$token = auth()->guard('triners')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->respondWithToken($token);

    }

    public function getCoursesByTrainerId($id){
        $courses = Trainer::with('courses')->find($id);
        if ($courses)
            return response()->json($courses, 200);
        else return response()->json("No courses for this trainer");
    }

    public function me()
    {
        return response()->json(auth()->guard('triners')->user());
    }


    public function logout()
    {
        auth()->guard('triners')->logout();
        return response()->json('Successfully logged out');
    }


    public function refresh()
    {
        return $this->respondWithToken(auth()->guard('triners')->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            // 'name'=>(Auth::guard('triners')->user()->fname+" "+Auth::guard('triners')->user()->lname),
            'name'=>Auth::guard('triners')->user()->fname,
            'id'=>Auth::guard('triners')->user()->id,
            'role'=>'isTrainer',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->guard('triners')->factory()->getTTL() * 120
        ],200);
    }

    public function sayHello(){
        return response()->json('hello trainers');
    }


}
