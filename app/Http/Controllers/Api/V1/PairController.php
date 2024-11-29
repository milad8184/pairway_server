<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController;
use App\Models\Pair;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $pair = Pair::where('user1_id', $user->id)->orWhere('user2_id', $user->id)->first();
        $partner = null;
        if ($user["partnerId"] != null && $user["partnerId"] !=  "") {
            $partnerRes = DB::select("SELECT * FROM user WHERE id=?", [$user->partnerId]);
            $partner = $partnerRes[0] ?? null;
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
        $pair = DB::table('pair')->where('connectkey', $code)->first();

        if ($pair == NULL || $pair["user2_id"] != NULL) {
            return $this->sendErro("wrongPair");
        }

        $loggedInUserId = $this->getUser()->id;

        $updated = DB::table('users')
            ->where('id', $loggedInUserId)
            ->update([
                'pair_id' => $pair->id,
                'partner_id' => $pair->user1_id,
            ]);

        $updated = DB::table('users')
            ->where('id', $pair->user1_id)
            ->update([
                'partner_id' => $loggedInUserId
            ]);

        $updated = $pair->update(["user2_id" => $loggedInUserId]);

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
