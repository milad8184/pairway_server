<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController;
use App\Models\Pair;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PairController extends BaseController
{

    public function index()
    {
        $id = Auth()->user()->id;
        $resp = ["id" => $id];

        return $this->sendResponse($resp);
    }


    public function get($uuid)
    {

        $pair = Pair::where('uuid', $uuid)->first();

        if ($pair == NULL) {
            return $this->sendError("pairNotFound");
        }

        if ($pair->user2_id != NULL) {
            return $this->sendError("alreadyRegistered");
        }

        $res = DB::select("SELECT id,name FROM user WHERE id=?", [$pair->user1_id]);
        if (count($res) < 1) {
            return $this->sendError("userNotFound");
        }
        $user = $res[0];
        if ($user == NULL) {
            return $this->sendError("userNotFound");
        }
        $resp = [
            "name" => $user->name,
            "pair" => $pair
        ];
        return $this->sendResponse($resp);
    }

    public function getOurData()
    {

        $user = $this->getUser();
        $pair = NULL;
        if($user->pair_id != null){
            $pair = Pair::where('id', $user->pair_id)->first();
        }
        $partner = null;
        if($user->partner_id != null){
            $partner = User::where('id', $user->partner_id)->first();
        }

        $resp = [
            "pair" => $pair,
            "user" => $user,
            "partner" => $partner
        ];
        return $this->sendResponse($resp);
    }

    public function connect(Request $request)
    {
        $code = $request->input('code');
        $user = DB::table('user')->where('connectkey', $code)->first();

        if ($user == NULL || $user->partner_id != NULL) {
            return $this->sendErro("wrongPair");
        }

        $loggedInUser = $this->getUser();

        $newPair = [];
        $newPair["user1_id"] = $loggedInUser->id;
        $newPair["user2_id"] = $user->id;
        $newPair["uuid"] = str_replace('-', '', Str::uuid());
        $newPair["created_at"] = now();
        $pair = Pair::create($newPair);

        $updated = DB::table('user')
            ->where('id', $user->id)
            ->update([
                'pair_id' => $pair->id,
                'partner_id' => $loggedInUser->id,
                'connectkey' => null
            ]);
        $updated = DB::table('user')
            ->where('id', $loggedInUser->id)
            ->update([
                'pair_id' => $pair->id,
                'partner_id' => $user->id,
                'connectkey' => null
            ]);

        if ($updated) {
            return $this->sendResponse(true);
        } else {
            return $this->sendErro("error");
        }
    }

    public function updateAnniversary(Request $request)
    {
        $pairId = $this->getUser()->pair_id;
        $anniversaryDate = $request->input('anniversary');

        $updated = DB::table('pair')
            ->where('id', $pairId)
            ->update(['anniversary_date' => $anniversaryDate]);

        if ($updated) {
            return $this->sendResponse(true);
        } else {
            return $this->sendErro("error");
        }
    }
}
