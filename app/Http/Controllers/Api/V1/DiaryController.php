<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController;
use App\Models\Diary;
use Illuminate\Http\Request;

class DiaryController extends BaseController
{
    public function index()
    {
        $id = Auth()->user()->id;
        $diaries = Diary::where('userid', $id)->get();
        return $this->sendResponse($diaries);
    }

    public function store(Request $request)
    {
        $entry = $request->all();
        $entry["userid"] = $this->getUser()->id;
        $entry["created_at"] = now();
        $resp = Diary::create($entry);
        return $this->sendResponse($resp);
    }
}
