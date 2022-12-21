<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOperationRequest;
use App\Models\Operation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class OperationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //abort_if(Gate::denies('operation_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOperationRequest $request)
    {
        /*$result = \DB::table('validation_workflows')->join('media', 'validation_workflows.media_id', '=', 'media.id')->select('model_id')
            ->where('validation_workflows.media_id', '=', 1)->get();
        dd($result);
        foreach ($result as $dev):
            dd($dev->model_id);
        endforeach;*/


        $newWorkflowValidate = Operation::create([
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
