<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreValidationWorkflowRequest;
use App\Models\Folder;
use App\Models\ValidationWorkflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class ValidationWorkflowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('validation_workflow_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreValidationWorkflowRequest $request)
    {
        $newWorkflowValidate = ValidationWorkflow::create([
            'deadline' => $request->deadline,
            'priority' => $request->priority,
            'status' => $request->visibility,
            'workflow_sender' => \Auth::user()->id,
            'workflow_receiver' => $request->user_assign,
            'media_id' => $request->media_id,
            'message' => $request->message,
            'receive_mail_notification' => $request->boolean('flexCheckChecked'),
        ]);

        return redirect()
            ->route('workflow.index', [$newWorkflowValidate])
            ->withStatus('Votre document a été envoyé avec succès.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
