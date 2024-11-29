<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnswerController extends BaseController
{

    public function store(Request $request)
    {
        $answerData = $request->all();
        $answerData["user_id"] = $this->getUser()->id;
        $resp = DB::table('answer')->updateOrInsert(
            [
                'question_id' => $answerData['question_id'],
                'user_id' => $answerData['user_id']
            ],
            [
                'answer_text' => $answerData['answer_text']
            ]
        );

        return $this->sendResponse($resp);
    }

    /**
     * Display the specified resource.
     */
    public function show() {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'street' => 'required',
            'zip' => 'required|string|min:5|max:5',
        ]);

        $id = Auth()->user()->companyid;
        $company = Answer::findOrFail($id);
        $company->update($request->all());
        return $this->sendResponse([]);
    }

    public function updateconfig(Request $request)
    {
        $request->validate([
            'maxmember' => 'required|numeric|min:7|max:500'
        ]);

        $id = Auth()->user()->companyid;
        $company = Answer::findOrFail($id);
        $company->update($request->all());
        return $this->sendResponse([]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Answer $answer)
    {
        //
    }
}
