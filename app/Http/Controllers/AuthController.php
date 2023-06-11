<?php

namespace App\Http\Controllers;

use Validator;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;


class AuthController extends Controller
{
  private function createToken() {
    $token = Auth::user()
      ->createToken(config('app.name'))
      ->accessToken;

    return response()->json([
      'user' => UserResource::make(Auth::user()),
      'token' => $token,
    ], Response::HTTP_OK);
  }

  public function login(Request $request)
  {
    $validator = Validator::make(
      $request->all(), 
      [
        'email' => 'required|email',
        'password' => 'required|string|min:8',
      ]
    );
    if ($validator->fails()) {
      return response()->json([
        'message' => $validator->getMessageBag()
      ], Response::HTTP_BAD_REQUEST);
    }

    if (Auth::attempt($request->only('email', 'password'))) {
      return $this->createToken();
    }
    
    return response()->json([
      'message' => ['email' => ['The provided credentials do not match our records.']],
    ], Response::HTTP_BAD_REQUEST);
  }

  public function register(Request $request)
  {
    $validator = Validator::make(
      $request->all(), 
      [
        'email' => 'required|email|unique:users',
        'name' => 'required|string|min:4|unique:users',
        'password' => 'required|string|min:8',
      ],
      [
        'email.unique' => 'E-Mail занят',
        'name.unique' => 'Имя занято',
      ]
    );
    if ($validator->fails()) {
      return response()->json([
        'message' => $validator->getMessageBag()
      ], Response::HTTP_BAD_REQUEST);
    }

    $user = User::create(array_merge(
      $request->only('name', 'email'),
      ['password' => bcrypt($request->password)]
    ));
    $user->save();

    Auth::attempt($request->only('email', 'password'));
    return $this->createToken();
  }

  public function logout() {
    Auth::user()->token()->revoke();

    return response()->json([
      'message' => 'Successfully logged out'
    ], Response::HTTP_OK);
  }

  public function getUserByToken() {
    $user = Auth::user();
    if ($user) {
      return response()->json(UserResource::make($user), Response::HTTP_OK);
    }
    return response()->json([
      "message" => "You aren't authorized"
    ], Response::HTTP_UNAUTHORIZED);
  }

  public function editUser(Request $request) {
    $validated = Validator::make($request->all(),
        [
            'image' => 'file',
            'name' => 'unique:users',
      ],
      [
        'name.unique' => 'Имя занято',
      ]
    );

    if ($validated->fails()) {
        return response()->json($validated->errors(), 400);
    }

    $user = Auth::user();

    if ($request->image) {
      if ($user->avatar && \Storage::exists($user->avatar)) {
        \Storage::delete($user->avatar);
      }

      $avatar = $request
        ->image
        ->storeAs(
          'images/avatar', 
          $user->name.'-'.date('d-m-y_H-i').'-genre.'.$request->image->extension(),
          'public'
        );
      $user->avatar = $avatar;
    }

    if ($request->name) {
      $user->name = $request->name;
    }

    if ($request->password) {
      $user->password = bcrypt($request->password);
    }

    $user->save();

    return response()->json(UserResource::make($user), Response::HTTP_OK);
  }
}