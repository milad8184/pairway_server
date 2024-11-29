<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController;
use App\Http\Resources\AnswerResource;
use App\Http\Resources\NoteResource;
use App\Models\Answer;
use App\Models\Note;
use App\Models\Question;
use App\Models\User;
use Illuminate\Http\Request;

class NoteController extends BaseController
{
    /**
     * Display a listing of the resource.
     */


    public function index()
    {
        $user = Auth()->user();
        $notes = Note::where('userid', $user->id)->get();

        $partnerName = NULL;
        if ($user->partner_id != NULL) {
            $partner = User::where('id', $user->partner_id)->first();
            if ($partner) {
                $partnerName = $partner->name;
            }
        }
        $resp = [
            "notes" => NoteResource::collection($notes),
            "partnerName" => $partnerName
        ];
        return $this->sendResponse($resp);
    }

    public function get($type)
    {
        $resp = Question::where('type', $type)->get();
        return $this->sendResponse($resp);
    }

    public function store(Request $request)
    {
        $note = $request->all();
        $note["userid"] = $this->getUser()->id;
        $resp = NoteResource::make(Note::create($note));
        return $this->sendResponse($resp);
    }

    public function destroy($id)
    {
        return $this->sendResponse(Note::destroy($id));
    }
}
