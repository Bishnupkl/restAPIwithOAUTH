<?php

namespace App\Http\Controllers;

use App\Models\User;
use Dotenv\Validator;
use Illuminate\Http\Request;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function register(Request  $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('MyApp')->accessToken;
        return $this->sendResponse($success, 'User registers successfully');
    }

    public function login(Request  $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password,
        ])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('MyApp')->accessToken;
            $success['name'] = $user->name;
            return $this->sendResponse($success,'User login succesfully');

        }else{
            return $this->sendError('unauthorised', ['error' => 'Unauthorised',
            ]);
        }

    }
}

