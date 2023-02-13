<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyFolderRequest;
use App\Models\Folder;
use App\Models\Operation;
use App\Models\Parapheur;
use App\Models\Project;
use App\Models\User;
use App\Notifications\sendEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Notification;
use setasign\Fpdi\Fpdi;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class WorkflowManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $allMedia = Media::with('operations')->whereNotNull('step_workflow')->get();

        /*foreach ($allMedia as $item){
            dd($item->toArray());
        }*/
        //dd($allMedia->toArray());
        //abort_if(Gate::denies('folder_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        //dd(Media::with('parapheur')->get()->toArray());
        return view('admin.workflow.index', compact('allMedia'));
    }

    public function create()
    {
        //abort_if(Gate::denies('folder_access_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $projects = Project::all()
            ->pluck('name', 'id')
            ->prepend(trans('global.pleaseSelect'), '');

        $users = User::where('id', '!=', \Auth::user()->id)
            ->whereHas('multiFolders')
            ->pluck('name', 'id')
            ->prepend(trans('global.pleaseSelect'), '');

        $folders = Folder::where('functionality', false)
            ->get()
            ->pluck('name', 'id')
            ->prepend(trans('global.pleaseSelect'), '');

        return view('admin.workflow.create', compact('users', 'folders', 'projects'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        //step 1: check visibility of file
        if($request->visibility == 'private'){
            //parapheur
            $parapheur = Parapheur::where('user_id', $request->user_list[0])->first();
            if($parapheur == null){
                $getLastInsertId = Parapheur::all()->max('id');
                $parapheur = Parapheur::create([
                    'name' => 'parapheur'. $getLastInsertId + 1,
                    'project_id' => 1,
                    'user_id' => $request->user_list[0]
                ]);
            }

            $workflow = [
                'step_workflow' => $request->user_list,
                'current_user' => [
                    'id' => $request->user_list[0],
                    'state' => 'pending'
                ]
            ];

            foreach ($request->input('files', []) as $file) {
                $media = $parapheur->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('files');
                $media->created_by = \Auth::user()->id;
                $media->parapheur_id = $parapheur->id;
                $media->model_id = 0;
                $media->version = $media->version + 1;
                $media->step_workflow = $workflow;

                $media->save();
                $media->usersListSelectedForWorkflowValidations()->sync([$request->user_list[0]]);

                Operation::create([
                    'deadline' => $request->deadline,
                    'priority' => $request->priority,
                    'status' => $request->visibility,
                    'user_id_sender' => \Auth::user()->id,
                    'user_id_receiver' => $request->user_list[0],
                    'media_id' => $media->id,
                    'message' => $request->message,
                    'receive_mail_notification' => $request->boolean('flexCheckChecked'),
                    'operation_type' => 'create_workflow',
                    'operation_state' => 'pending',
                    'num_operation' => (string) Str::orderedUuid(),
                ]);


                //if checkbox of email checked, then email is send at receiver
                if($request->boolean('flexCheckChecked')){
                    $getUser = User::find($request->user_assign);
                    $details = [
                        'greeting' => 'Bonjour '. $getUser->name,
                        'body' => 'Vous avez réçu un message de '. auth()->user()->name . ': ' .$request->message,
                        'actiontext' => 'Subscribe this channel',
                        'actionurl' => '/',
                        'lastline' => 'Nous vous remercions pour votre bonne comprehension.'
                    ];
                    Notification::send($getUser, new sendEmailNotification($details));
                }
            }


        }
        else{
            //Get Folder
            $user = User::with('multiFolders')->where('id', $request->user_list[0])->first();
            $folder = $user->multiFolders->first();

            $workflow = [
                'step_workflow' => $request->user_list,
                'current_user' => [
                    'id' => $request->user_list[0],
                    'state' => 'pending'
                ]
            ];

            foreach ($request->input('files', []) as $file) {
                //Create Media table with new datas for receiver user
                $media = $folder->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('files');
                $media->version = $media->version + 1;
                $media->step_workflow = $workflow;
                $media->save();
                $media->usersListSelectedForWorkflowValidations()->sync([$request->user_list[0]]);

                Operation::create([
                    'deadline' => $request->deadline,
                    'priority' => $request->priority,
                    'status' => $request->visibility,
                    'user_id_sender' => \Auth::user()->id,
                    'user_id_receiver' => $request->user_list[0],
                    'media_id' => $media->id,
                    'message' => $request->message,
                    'receive_mail_notification' => $request->boolean('flexCheckChecked'),
                    'operation_type' => 'create_workflow',
                    'operation_state' => 'pending',
                    'num_operation' => (string) Str::orderedUuid(),
                ]);


                //if checkbox of email checked, then email is send at receiver
                if($request->boolean('flexCheckChecked')){
                    $getUser = User::find($request->user_assign);
                    $details = [
                        'greeting' => 'Bonjour '. $getUser->name,
                        'body' => 'Vous avez réçu un message de '. auth()->user()->name . ': ' .$request->message,
                        'actiontext' => 'Subscribe this channel',
                        'actionurl' => '/',
                        'lastline' => 'Nous vous remercions pour votre bonne comprehension.'
                    ];
                    Notification::send($getUser, new sendEmailNotification($details));
                }
            }
        }

        return redirect()->route('admin.workflow-management.index');
    }


    public function storeMedia(Request $request)
    {
        // Validates file size
        if (request()->has('size')) {
            $this->validate(request(), [
                'file' => 'max:' . request()->input('size') * 1024,
            ]);
        }

        // If width or height is preset - we are validating it as an image
        if (request()->has('width') || request()->has('height')) {
            $this->validate(request(), [
                'file' => sprintf(
                    'image|dimensions:max_width=%s,max_height=%s',
                    request()->input('width', 100000),
                    request()->input('height', 100000)
                ),
            ]);
        }

        $path = storage_path('tmp/uploads');

        try {
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
        } catch (\Exception $e) {
        }

        $file = $request->file('file');

        $name = uniqid() . '_' . trim($file->getClientOriginalName());

        $file->move($path, $name);

        return response()->json([
            'name'          => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        /*$user = User::where('id', $id)->get();
        return response()->json($user);*/

        abort_if(Gate::denies('workflow_management_access_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $media = Media::with('operations')
            ->whereNotNull('step_workflow')
            ->findOrFail($id);

        return view('admin.workflow.show', compact('media'));
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
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        abort_if(Gate::denies('workflow_management_access_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        Operation::where('id', $id)
            ->delete();

        return back();
    }

    public function massDestroy(MassDestroyFolderRequest $request)
    {
        Operation::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }


    public function hasReadMedia(Request $request){

        $mediaAndOperation = Operation::where([
            ['media_id', $request->id],
            ['user_id_receiver', \Auth::user()->id]
        ])->first();

        if($mediaAndOperation != null){
            $mediaAndOperation->update([
                'receiver_read_doc' => true,
                'receiver_read_doc_at' => date('Y-m-d H:i:s', time())
            ]);
        }

        //$infoUser = User::with(['receiveOperations', 'sendOperations', 'multiFolders'])->find(auth()->id());
        $infoMedia = Media::with(['parapheur', 'category', 'operations', 'createdBy', 'signedBy'])->find($request->id);
        /*foreach ($infoMedia->operations as $parapheurItem){
            dd($parapheurItem->toArray());
        }*/

        $allUser = User::with(['receiveOperations', 'sendOperations'])->get();
        $userData = [];

        foreach($allUser as $user){

            $userData = $user;
            /*foreach($user->receiveOperations as $item){
                dd($item->toArray());
            }
            foreach($user->sendOperations as $item2){
                dd($item2->toArray());
            }*/
        }
        //dd($infoMedia->toArray());

        return response()->json([
            'media' => $infoMedia,
            'user' => $allUser,
            'workflow_validation' => $infoMedia->step_workflow
        ], Response::HTTP_OK);
    }


    public function validateDocument(Request $request){
        $getMediaDocument = Media::with('operations')->find($request->id);

        switch ($request->validationType){
            case "validation":

                //get and update of step workflow
                $workflow = [
                        'id' => auth()->id(),
                        'state' => 'pending'
                ];


                $oldValue = json_decode($getMediaDocument->step_workflow);
                //dd([$oldValue->current_user]);


                $collection = collect([
                    $oldValue->current_user,
                ]);

                //$collection->push($workflow);

                $collection->all();

                dd($collection);




                $getMediaDocument->step_workflow = \Arr::add([$oldValue->current_user], $workflow);
                dd($getMediaDocument->step_workflow);
                //update media table
                /*$getMediaDocument->version = $getMediaDocument->version + 1;
                $getMediaDocument->signing = 1;
                $getMediaDocument->save();*/



                /*$getMediaWithOperationDocument = $getMediaDocument->operations->first();
                if($getMediaWithOperationDocument != null){
                    //store data in operation table
                    Operation::create([
                        'deadline' => $getMediaWithOperationDocument->deadline,
                        'priority' => $getMediaWithOperationDocument->priority,
                        'status' => $getMediaWithOperationDocument->status,
                        'user_id_sender' => \Auth::user()->id,
                        'user_id_receiver' => $request->user_assign,
                        'media_id' => $request->id,
                        'message' => $getMediaWithOperationDocument->message,
                        'receive_mail_notification' => $getMediaWithOperationDocument->receive_mail_notification,
                        'operation_type' => $request->validationType,
                        'operation_state' => 'pending',
                        'num_operation' => (string) Str::orderedUuid(),
                    ]);
                }*/

            break;
            case "validation_signature":
                //$path = storage_path($getMediaDocument->file_name);
                $filePath = $getMediaDocument->getPath();
                //$filePath = asset('uploads/official.pdf');
                $outputFilePath = $getMediaDocument->getPath();
                //$outputFilePath = asset('uploads/official.pdf');
                $this->fillPDFFile($filePath, $outputFilePath);
                //return response()->file($outputFilePath);

                //update media table
                $getMediaDocument->signing = 1;
                $getMediaDocument->save();
                break;
            case "validation_paraphe":
                //update media table
                /*$getMediaDocument->signing = 1;
                $getMediaDocument->save();*/
                break;
        }

        return response()->json([
            'title' => 'Votre validation a été effectué avec succès'
        ]);

    }

    /**
     * Write code on Method
     *
     * @return string()
     */
    public function fillPDFFile($file, $outputFilePath)
    {
        $fpdi = new FPDI;

        $count = $fpdi->setSourceFile($file);

        for ($i=1; $i<=$count; $i++) {

            $template = $fpdi->importPage($i);
            $size = $fpdi->getTemplateSize($template);
            $fpdi->AddPage($size['orientation'], array($size['width'], $size['height']));
            $fpdi->useTemplate($template);

            $fpdi->SetFont("helvetica", "", 15);
            $fpdi->SetTextColor(153,0,153);

            $left = 10;
            $top = 10;
            $text = "NiceSnippets.com";
            $fpdi->Text($left,$top,$text);

            $getSignature = Media::where('signed_by', auth()->id())->first();

            $fpdi->Image($getSignature->getPath(), 40, 90);

            //$fpdi->Image("file:///var/www/example-app/public/nice-logo.png", 40, 90);
        }

        return $fpdi->Output($outputFilePath, 'F');
    }

}
