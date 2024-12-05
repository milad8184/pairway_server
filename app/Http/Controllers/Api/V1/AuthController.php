<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\PairResource;
use App\Http\Resources\UserResource;
use App\Models\Pair;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseController
{

    public function login(Request $request)
    {

        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        $user =  User::where('email', $fields['email'])->first();

        if (!$user) {
            return $this->sendError('userNotFound', [], 200);
        }

        if (!Hash::check($fields['password'], $user->password)) {
            return $this->sendError('wrongDetails', [], 200);
        }

        $token = $user->createToken('afe34vf324dfGU45')->plainTextToken;
        $data['token'] =  $token;
        $data['user'] =  UserResource::make($user);

        // $pair = Pair::find($user->pair_id);


        /*  if ($pair->user2_id == NULL) {
            return $this->sendError('notGrouped', [$pair->uuid], 200);
        } */
        //  $data['pair'] = PairResource::make($pair);
        // $partnerId = $pair->user1_id == $user->id ? $pair->user2_id : $pair->user1_id;
        // $data['partner'] =  UserResource::make(User::where('id',$partnerId)->first());
        return $this->sendResponse($data, 'successfully.');
    }

    public function loginnative(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string',
            'id' => 'required|string',
            'registertype' => 'required|string'
        ]);

        $user = User::where([
            ['nativeid', '=', $fields['id']],
            ['registertype', '=', $fields['registertype']],
        ])->first();

        if (!$user) {
            return $this->sendError('userNotFound', [], 200);
        }

        if ($fields['email'] != $user->email) {
            return $this->sendError('wrongDetails', [], 200);
        }

        $token = $user->createToken('afe34vf324dfGU45')->plainTextToken;
        $data['token'] =  $token;
        $data['user'] =  UserResource::make($user);

        // $pair = Pair::find($user->pair_id);


        /*  if ($pair->user2_id == NULL) {
            return $this->sendError('notGrouped', [$pair->uuid], 200);
        } */
        //  $data['pair'] = PairResource::make($pair);
        // $partnerId = $pair->user1_id == $user->id ? $pair->user2_id : $pair->user1_id;
        // $data['partner'] =  UserResource::make(User::where('id',$partnerId)->first());
        return $this->sendResponse($data, 'successfully.');
    }

    public function checkGroupId(Request $request)
    {

        $fields = $request->validate([
            'groupId' => 'required|string'
        ]);

        $pair =  Pair::where('uuid', $fields['groupId'])->first();

        if (!$pair) {
            return $this->sendError('pairNotFound', [], 200);
        }

        $user =  User::where('id', $pair->user1_id)->first();
        $data["name"] = $user->name;
        $data["groupid"] = $pair->id;
        return $this->sendResponse($data, 'successfully.');
    }

    public function checkEmail(Request $request)
    {

        $fields = $request->validate([
            'email' => 'required|string'
        ]);

        $user = User::where('email',  $fields['email'])->first();
        if ($user) {
            return $this->sendError("userExists");
        }

        return $this->sendResponse(true, 'successfully.');
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return $this->sendResponse([], 'successfully.');
    }
}
