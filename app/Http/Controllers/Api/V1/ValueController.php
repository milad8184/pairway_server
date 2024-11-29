<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController;
use App\Http\Resources\AnswerResource;
use App\Http\Resources\ValueResource;
use App\Models\Value;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ValueController extends BaseController
{
    /**
     * Display a listing of the resource.
     */


    public function rules()
    {
        $user = $this->getUser();
        $id = $user->id;
        $pairid = $user->pair_id;
        //$entries = DB::select('SELECT v.*,uv.user_id FROM value v LEFT JOIN user_value uv ON v.id = uv.value_id AND uv.user_id =? WHERE v.type = 2',[$id]);
        $res = DB::select('SELECT id FROM user_value WHERE user_id=? AND type=1', [$id]);
        $userHasSelectedAlready = false;
        $commonrules = NULL;
        $rules = NULL;
        if (count($res) > 0) {
            $userHasSelectedAlready = true;
            $commonrules = DB::select('SELECT DISTINCT v.* FROM value v JOIN user_value uv1 ON v.id = uv1.value_id JOIN user_value uv2 ON v.id = uv2.value_id JOIN pair p ON (uv1.user_id = p.user1_id AND uv2.user_id = p.user2_id) OR (uv1.user_id = p.user2_id AND uv2.user_id = p.user1_id) WHERE v.type=1 AND (p.user1_id = ? OR p.user2_id = ?) AND (uv1.user_id = ? OR uv2.user_id = ?)', [$id, $id, $id, $id]);
        } 
        $rules = DB::select('SELECT * FROM value WHERE (type=1 AND created_by_pair=0) OR (type=1 AND created_by_pair=?)', [$pairid]);
        $selectedIds = DB::select('SELECT value_id FROM user_value WHERE type=1 AND user_id=?', [$id]);
        if(count($selectedIds) > 0) {
            $selectedIds = array_map(function($item) {
                return $item->value_id;
            }, $selectedIds);
        }
        $data['userHasSelectedAlready'] = $userHasSelectedAlready;
        $data["commonrules"] = $commonrules;
        $data["rules"] = $rules;
        $data["selectedIds"] = $selectedIds;
        return $this->sendResponse($data);
    }


    public function prefs()
    {
        $id = Auth()->user()->id;
        //$entries = DB::select('SELECT v.*,uv.user_id FROM value v LEFT JOIN user_value uv ON v.id = uv.value_id AND uv.user_id =? WHERE v.type = 2',[$id]);
        $res = DB::select('SELECT id FROM user_value WHERE user_id=? AND type=2', [$id]);
        $userHasSelectedAlready = false;
        $commonprefs = NULL;
        $prefs = NULL;
        if (count($res) > 0) {
            $userHasSelectedAlready = true;
            $commonprefs = DB::select('SELECT DISTINCT v.* FROM value v JOIN user_value uv1 ON v.id = uv1.value_id JOIN user_value uv2 ON v.id = uv2.value_id JOIN pair p ON (uv1.user_id = p.user1_id AND uv2.user_id = p.user2_id) OR (uv1.user_id = p.user2_id AND uv2.user_id = p.user1_id) WHERE v.type=2 AND (p.user1_id = ? OR p.user2_id = ?) AND (uv1.user_id = ? OR uv2.user_id = ?)', [$id, $id, $id, $id]);
        } else {
            $prefs = DB::select('SELECT * FROM value WHERE type=2');
        }
        $data['userHasSelectedAlready'] = $userHasSelectedAlready;
        $data["commonprefs"] = $commonprefs;
        $data["prefs"] = $prefs;
        return $this->sendResponse($data);
    }

    public function commonprefs()
    {
        $id = Auth()->user()->id;
        $entries = DB::select('SELECT DISTINCT v.* FROM value v JOIN user_value uv1 ON v.id = uv1.value_id JOIN user_value uv2 ON v.id = uv2.value_id JOIN pair p ON (uv1.user_id = p.user1_id AND uv2.user_id = p.user2_id) OR (uv1.user_id = p.user2_id AND uv2.user_id = p.user1_id) WHERE v.type=2 AND (p.user1_id = ? OR p.user2_id = ?) AND (uv1.user_id = ? OR uv2.user_id = ?)', [$id, $id, $id, $id]);
        return $this->sendResponse($entries);
    }

    public function commonrules()
    {
        $id = Auth()->user()->id;
        $entries = DB::select('SELECT DISTINCT v.* FROM value v JOIN user_value uv1 ON v.id = uv1.value_id JOIN user_value uv2 ON v.id = uv2.value_id JOIN pair p ON (uv1.user_id = p.user1_id AND uv2.user_id = p.user2_id) OR (uv1.user_id = p.user2_id AND uv2.user_id = p.user1_id) WHERE v.type=1 AND (p.user1_id = ? OR p.user2_id = ?) AND (uv1.user_id = ? OR uv2.user_id = ?)', [$id, $id, $id, $id]);
        return $this->sendResponse($entries);
    }

    public function store(Request $request)
    {
        $request->validate([
            'value_ids' => 'required|string',
            'type' => 'required|int'
        ]);

        $userId = Auth()->user()->id;
        $type = $request->type;
        $valueIds = array_map('intval', explode(',', $request->value_ids));

        // Löschen aller vorhandenen user_value-Einträge für diesen Benutzer
        DB::table('user_value')->where(['user_id' => $userId, 'type' => $type])->delete();
        foreach ($valueIds as $valueId) {
            DB::table('user_value')->insert([
                'user_id' => $userId,
                'value_id' => $valueId,
                'type' => $type
            ]);
        }
        return $this->sendResponse(true);
    }

    public function storeRule(Request $request)
    {
        $entry = $request->all();
        $entry["type"] = 1;
        $entry["created_by_pair"] = $this->getUser()->pair_id;
        $resp = Value::create($entry);
        return $this->sendResponse(ValueResource::make($resp));
    }


    public function destroy($id)
    {
        return $this->sendResponse(Value::destroy($id));
    }
}
