<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyFolderRequest;
use App\Models\AuditLog;
use App\Models\Folder;
use App\Models\Operation;
use App\Models\Parapheur;
use App\Models\Project;
use App\Models\User;
use App\Notifications\sendEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Notification;
use setasign\Fpdi\Fpdi;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Traits\Auditable;

class WorkflowManagementController extends Controller
{
    use Auditable;
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
        $globalDeadline = Carbon::parse($request->deadline);
        $getUserListForWorkflowValidation = collect();
        $usersId = $request->user_list;
        //Get ID of first main user or delegate user
        $firstUserId = $request->get("user_list".$usersId[0]) != null ? $request->get("user_list".$usersId[0]) : $usersId[0];


        for($i=0; $i<count($usersId); $i++){

            $smallDeadline = Carbon::parse($request->get("deadline".$usersId[$i]));

            if($globalDeadline->lessThan($smallDeadline)){
                //deadline global inférieur
                return back()->withInput($request->input())->withErrors(['errors' => 'Le deadline d\'un ou plusieurs utilisateurs est supérieur au deadline global du circuit de validation' ]);
            }

            //si userList est différent de null alors nous avons une délégation de signature
            $getUserListForWorkflowValidation->push([
                'id' => $i,
                'user_id' => $request->get("user_list".$usersId[$i]) != null ? $request->get("user_list".$usersId[$i]) : $usersId[$i],
                'state' => 'pending',
                'deadline' => $request->get("deadline".$usersId[$i])
            ]);
        }

        //dd($getUserListForWorkflowValidation->all());



        //step 1: check visibility of file
        if($request->visibility == 'private'){
           //parapheur
            $parapheur = Parapheur::where('user_id', $firstUserId)->first();
            if($parapheur == null){
                $getLastInsertId = Parapheur::all()->max('id');
                $parapheur = Parapheur::create([
                    'name' => 'parapheur'. $getLastInsertId + 1,
                    'project_id' => 1,
                    'user_id' => $firstUserId
                ]);
            }

            foreach ($request->input('files', []) as $file) {
                $media = $parapheur->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('files');
                $media->created_by = \Auth::user()->id;
                $media->parapheur_id = $parapheur->id;
                $media->model_id = 0;
                $media->version = $media->version + 1;
                $media->step_workflow = $getUserListForWorkflowValidation->all();

                $media->save();
                $media->usersListSelectedForWorkflowValidations()->sync([$firstUserId]);

                Operation::create([
                    'deadline' => $request->deadline,
                    'priority' => $request->priority,
                    'status' => $request->visibility,
                    'user_id_sender' => \Auth::user()->id,
                    'user_id_receiver' => $firstUserId,
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
            $user = User::with('multiFolders')->where('id', $firstUserId)->first();
            $folder = $user->multiFolders->first();

            foreach ($request->input('files', []) as $file) {
                //Create Media table with new datas for receiver user
                $media = $folder->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('files');
                $media->version = $media->version + 1;
                $media->step_workflow = $getUserListForWorkflowValidation->all();
                $media->save();
                $media->usersListSelectedForWorkflowValidations()->sync([$firstUserId]);

                Operation::create([
                    'deadline' => $request->deadline,
                    'priority' => $request->priority,
                    'status' => $request->visibility,
                    'user_id_sender' => \Auth::user()->id,
                    'user_id_receiver' => $firstUserId,
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

    public function openDocument(Request $request){
        $getLog = AuditLog::where('media_id', $request->id)->where('operation_type', 'OPEN_DOCUMENT')->get();
        if(count($getLog) === 0){
            self::trackOperations($request->id,
                "OPEN_DOCUMENT",
                auth()->user()->name .' vient d\'ouvrir le document '. $request->name,
                'success',
                null,
                auth()->id(),
                auth()->user()->name);
        }
        return \response()->json($getLog);
    }

    public function hasReadMedia(Request $request){

        $getLog = AuditLog::where('media_id', $request->id)->where('operation_type', 'PREVIEW_DOCUMENT')->get();
        if(count($getLog) === 0){
            self::trackOperations($request->id,
                "PREVIEW_DOCUMENT",
                auth()->user()->name .' vient de voir le document '. $request->name,
                'success',
                null,
                auth()->id(),
                auth()->user()->name);
        }


        /*$mediaAndOperation = Operation::where([
            ['media_id', $request->id],
            ['user_id_receiver', \Auth::user()->id]
        ])->first();

        if($mediaAndOperation != null){
            $mediaAndOperation->update([
                'receiver_read_doc' => true,
                'receiver_read_doc_at' => date('Y-m-d H:i:s', time())
            ]);
        }

        $infoMedia = Media::with(['parapheur', 'category', 'createdBy', 'signedBy', 'operations' => function($q){
            $q->orderBy('id', 'DESC');
        }])->find($request->id);

        $allUser = User::with(['receiveOperations', 'sendOperations'])->get();

        return response()->json([
            'media' => $infoMedia,
            'user' => $allUser,
            'workflow_validation' => $infoMedia->step_workflow
        ], Response::HTTP_OK);*/

        $getLog = AuditLog::where('media_id', $request->id)->get();
        //dd($getLog->toArray());
        return response()->json([
            'tracking' => $getLog,
        ], Response::HTTP_OK);
    }


    public function validateDocument(Request $request){
        $getMediaDocument = Media::with('operations')->find($request->id);
        $getLog = AuditLog::where('media_id', $getMediaDocument->id);
        $idNextUser = "";
        $counterPreviousUser = 0;
        $oldValue = json_decode($getMediaDocument->step_workflow);
        for ($i =0; $i<count($oldValue); $i++){
            //check if all users are pending
            if($oldValue[$i]->state == "pending"){

                //check if user connected exist in workflow if yes then update state
                if($oldValue[$i]->user_id == auth()->id()){
                    $oldValue[$i]->state = "finish";
                    $counterPreviousUser = $oldValue[$i]->id;
                }
                //get id of next user
                if($counterPreviousUser + 1 == $i){
                    $idNextUser = $oldValue[$i]->user_id;
                }
            }
        }

        switch ($request->validationType){
            case "validation":
                if($idNextUser != ""){
                    $getMediaWithOperationDocument = $getMediaDocument->operations->first();
                    if($getMediaWithOperationDocument != null){

                        if($getMediaWithOperationDocument->status == "public"){

                            $user = User::with('multiFolders')->where('id', $idNextUser)->first();
                            $folder = $user->multiFolders->first();
                            //update media table
                            $getMediaDocument->version = $getMediaDocument->version + 1;
                            $getMediaDocument->model_id = $folder->id;
                            $getMediaDocument->step_workflow = $oldValue;
                            $getMediaDocument->save();

                            $getMediaDocument->usersListSelectedForWorkflowValidations()->sync([$idNextUser]);

                        }else{

                            $parapheur = Parapheur::where('user_id', $idNextUser)->first();
                            if($parapheur == null){
                                $getLastInsertId = Parapheur::all()->max('id');
                                $parapheur = Parapheur::create([
                                    'name' => 'parapheur'. $getLastInsertId + 1,
                                    'project_id' => 1,
                                    'user_id' => $idNextUser
                                ]);
                            }

                            //update media table
                            $getMediaDocument->version = $getMediaDocument->version + 1;
                            $getMediaDocument->parapheur_id = $parapheur->id;
                            $getMediaDocument->step_workflow = $oldValue;
                            $getMediaDocument->save();

                            $getMediaDocument->usersListSelectedForWorkflowValidations()->sync([$idNextUser]);

                        }

                        //store datas operation table
                        Operation::create([
                            'deadline' => $getMediaWithOperationDocument->deadline,
                            'priority' => $getMediaWithOperationDocument->priority,
                            'status' => $getMediaWithOperationDocument->status,
                            'user_id_sender' => auth()->id(),
                            'user_id_receiver' => $idNextUser,
                            'media_id' => $getMediaDocument->id,
                            'message' => $getMediaWithOperationDocument->message,
                            'receive_mail_notification' => $getMediaWithOperationDocument->receive_mail_notification,
                            'operation_type' => $request->validationType,
                            'operation_state' => 'pending',
                            'num_operation' => (string) Str::orderedUuid(),
                        ]);

                        $getOperations = Operation::where('media_id', $getMediaDocument->id)
                            ->where('user_id_receiver', auth()->id())
                            //->orWhere('user_id_sender', auth()->id())
                            ->get();

                        foreach ($getOperations as $operationList){
                            $operationList->update(['operation_state' => 'success']);
                        }

                        //save data for tracking
                        $getLog = $getLog->where('operation_type', 'VALIDATE_DOCUMENT')->get();
                       if(count($getLog) === 0){
                           //mode receiver
                            self::trackOperations($getMediaDocument->id,
                                "VALIDATE_DOCUMENT",
                                auth()->user()->name .' vient de valider le document '. substr($getMediaDocument->file_name, 14),
                                'success',
                                null,
                                auth()->id(),
                                auth()->user()->name,
                                $getMediaWithOperationDocument->message);

                            $getDataNextUser = User::findOrFail($idNextUser);
                                self::trackOperations($getMediaDocument->id,
                                    "SEND_DOCUMENT",
                                    $getDataNextUser->name .' a un document en attente de validation ',
                                    'pending',
                                    auth()->id(),
                                    $idNextUser,
                                    auth()->user()->name,
                                    $getMediaWithOperationDocument->message);

                        }
                    }
                }
            break;
            case "validation_signature":
                if($idNextUser != ""){
                    $getMediaWithOperationDocument = $getMediaDocument->operations->first();
                    if($getMediaWithOperationDocument != null){

                        if($getMediaWithOperationDocument->status == "public"){
                            $user = User::with('multiFolders')->where('id', $idNextUser)->first();
                            $folder = $user->multiFolders->first();
                            //update media table
                            $getMediaDocument->version = $getMediaDocument->version + 1;
                            $getMediaDocument->model_id = $folder->id;
                            $getMediaDocument->step_workflow = $oldValue;
                            $getMediaDocument->save();

                            $getMediaDocument->usersListSelectedForWorkflowValidations()->sync([$idNextUser]);

                        }else{

                            $parapheur = Parapheur::where('user_id', $idNextUser)->first();
                            if($parapheur == null){
                                $getLastInsertId = Parapheur::all()->max('id');
                                $parapheur = Parapheur::create([
                                    'name' => 'parapheur'. $getLastInsertId + 1,
                                    'project_id' => 1,
                                    'user_id' => $idNextUser
                                ]);
                            }

                            //update media table
                            $getMediaDocument->version = $getMediaDocument->version + 1;
                            $getMediaDocument->parapheur_id = $parapheur->id;
                            $getMediaDocument->step_workflow = $oldValue;
                            $getMediaDocument->save();

                            $getMediaDocument->usersListSelectedForWorkflowValidations()->sync([$idNextUser]);

                        }

                        //store datas operation table
                        Operation::create([
                            'deadline' => $getMediaWithOperationDocument->deadline,
                            'priority' => $getMediaWithOperationDocument->priority,
                            'status' => $getMediaWithOperationDocument->status,
                            'user_id_sender' => auth()->id(),
                            'user_id_receiver' => $idNextUser,
                            'media_id' => $getMediaDocument->id,
                            'message' => $getMediaWithOperationDocument->message,
                            'receive_mail_notification' => $getMediaWithOperationDocument->receive_mail_notification,
                            'operation_type' => $request->validationType,
                            'operation_state' => 'pending',
                            'num_operation' => (string) Str::orderedUuid(),
                        ]);

                        $getOperations = Operation::where('media_id', $getMediaDocument->id)
                            ->where('user_id_receiver', auth()->id())
                            //->orWhere('user_id_sender', auth()->id())
                            ->get();

                        foreach ($getOperations as $operationList){
                            $operationList->update(['operation_state' => 'success']);
                        }

                        //----------------------------------------------------------------------------------------
                        //save data for tracking
                        $getLog = $getLog->where('operation_type', 'VALIDATE_DOCUMENT_SIGNATURE')->get();
                        if(count($getLog) === 0){
                            self::trackOperations($getMediaDocument->id,
                                "VALIDATE_DOCUMENT_SIGNATURE",
                                auth()->user()->name .' vient de signer le document '. substr($getMediaDocument->file_name, 14),
                                'success',
                                null,
                                auth()->id(),
                                auth()->user()->name,
                                $getMediaWithOperationDocument->message);

                            $getDataNextUser = User::findOrFail($idNextUser);
                            self::trackOperations($getMediaDocument->id,
                                "SEND_DOCUMENT_SIGNATURE",
                                $getDataNextUser->name .' a envoyé un document en attente de signature ',
                                'pending',
                                auth()->id(),
                                $idNextUser,
                                auth()->user()->name,
                                $getMediaWithOperationDocument->message);

                        }

                        $getMedia = Media::with('operations')->find($request->id);
                        //$path = storage_path($getMediaDocument->file_name);
                        $filePath = $getMedia->getPath();
                        //$filePath = asset('uploads/official.pdf');
                        $outputFilePath = $getMedia->getPath();
                        //$outputFilePath = asset('uploads/official.pdf');
                        $this->fillPDFFileSignature($filePath, $outputFilePath);
                        //return response()->file($outputFilePath);
                    }
                }
                break;
            case "validation_paraphe":
                if($idNextUser != ""){
                    $getMediaWithOperationDocument = $getMediaDocument->operations->first();
                    if($getMediaWithOperationDocument != null){

                        if($getMediaWithOperationDocument->status == "public"){
                            $user = User::with('multiFolders')->where('id', $idNextUser)->first();
                            $folder = $user->multiFolders->first();
                            //update media table
                            $getMediaDocument->version = $getMediaDocument->version + 1;
                            $getMediaDocument->model_id = $folder->id;
                            $getMediaDocument->step_workflow = $oldValue;
                            $getMediaDocument->save();

                            $getMediaDocument->usersListSelectedForWorkflowValidations()->sync([$idNextUser]);

                        }else{

                            $parapheur = Parapheur::where('user_id', $idNextUser)->first();
                            if($parapheur == null){
                                $getLastInsertId = Parapheur::all()->max('id');
                                $parapheur = Parapheur::create([
                                    'name' => 'parapheur'. $getLastInsertId + 1,
                                    'project_id' => 1,
                                    'user_id' => $idNextUser
                                ]);
                            }

                            //update media table
                            $getMediaDocument->version = $getMediaDocument->version + 1;
                            $getMediaDocument->parapheur_id = $parapheur->id;
                            $getMediaDocument->step_workflow = $oldValue;
                            $getMediaDocument->save();

                            $getMediaDocument->usersListSelectedForWorkflowValidations()->sync([$idNextUser]);

                        }

                        //store datas operation table
                        Operation::create([
                            'deadline' => $getMediaWithOperationDocument->deadline,
                            'priority' => $getMediaWithOperationDocument->priority,
                            'status' => $getMediaWithOperationDocument->status,
                            'user_id_sender' => auth()->id(),
                            'user_id_receiver' => $idNextUser,
                            'media_id' => $getMediaDocument->id,
                            'message' => $getMediaWithOperationDocument->message,
                            'receive_mail_notification' => $getMediaWithOperationDocument->receive_mail_notification,
                            'operation_type' => $request->validationType,
                            'operation_state' => 'pending',
                            'num_operation' => (string) Str::orderedUuid(),
                        ]);

                        $getOperations = Operation::where('media_id', $getMediaDocument->id)
                            ->where('user_id_receiver', auth()->id())
                            //->orWhere('user_id_sender', auth()->id())
                            ->get();

                        foreach ($getOperations as $operationList){
                            $operationList->update(['operation_state' => 'success']);
                        }


                        //----------------------------------------------------------------------------------------
                        //save data for tracking
                        $getLog = $getLog->where('operation_type', 'VALIDATE_DOCUMENT_PARAPHEUR')->get();
                        if(count($getLog) === 0){
                            self::trackOperations($getMediaDocument->id,
                                "VALIDATE_DOCUMENT_PARAPHEUR",
                                auth()->user()->name .' vient de parapher le document '. substr($getMediaDocument->file_name, 14),
                                'success',
                                auth()->id(),
                                $idNextUser,
                                auth()->user()->name,
                                $getMediaWithOperationDocument->message);

                            $getDataNextUser = User::findOrFail($idNextUser);
                            self::trackOperations($getMediaDocument->id,
                                "SEND_DOCUMENT_PARAPHEUR",
                                $getDataNextUser->name .' a envoyé un document en attente d\'être paraphé ',
                                'pending',
                                auth()->id(),
                                $idNextUser,
                                auth()->user()->name,
                                $getMediaWithOperationDocument->message);

                        }

                        $getMedia = Media::with('operations')->find($request->id);
                        $filePath = $getMedia->getPath();
                        $outputFilePath = $getMedia->getPath();
                        $this->fillPDFFileParaphe($filePath, $outputFilePath);
                    }
                }
                break;

            case "rejected":
                $getIdPreviousUser = $getMediaDocument->operations->where('user_id_receiver', auth()->id())->first()->user_id_sender;
                $oldValue = json_decode($getMediaDocument->step_workflow);
                for ($i =0; $i<count($oldValue); $i++){
                    //check if all users are pending
                    if($oldValue[$i]->state == "finish"){
                        //check if user connected exist in workflow if yes then update state
                        if($oldValue[$i]->user_id == auth()->id()){
                            $oldValue[$i]->state = "pending";
                        }
                    }
                }

                    //$getDataNextUser = User::findOrFail($idNextUser);
                    $getMediaWithOperationDocument = $getMediaDocument->operations->first();
                    if($getMediaWithOperationDocument != null){

                        if($getMediaWithOperationDocument->status == "public"){

                            $user = User::with('multiFolders')->where('id', $getIdPreviousUser)->first();
                            $folder = $user->multiFolders->first();
                            //update media table
                            $getMediaDocument->version = $getMediaDocument->version + 1;
                            $getMediaDocument->model_id = $folder->id;
                            $getMediaDocument->step_workflow = $oldValue;
                            $getMediaDocument->save();

                            $getMediaDocument->usersListSelectedForWorkflowValidations()->sync([$getIdPreviousUser]);

                        }else{

                            $parapheur = Parapheur::where('user_id', $getIdPreviousUser)->first();
                            if($parapheur == null){
                                $getLastInsertId = Parapheur::all()->max('id');
                                $parapheur = Parapheur::create([
                                    'name' => 'parapheur'. $getLastInsertId + 1,
                                    'project_id' => 1,
                                    'user_id' => $getIdPreviousUser
                                ]);
                            }

                            //update media table
                            $getMediaDocument->version = $getMediaDocument->version + 1;
                            $getMediaDocument->parapheur_id = $parapheur->id;
                            $getMediaDocument->step_workflow = $oldValue;
                            $getMediaDocument->save();

                            $getMediaDocument->usersListSelectedForWorkflowValidations()->sync([$getIdPreviousUser]);

                        }

                        //store datas operation table
                        Operation::create([
                            'deadline' => $getMediaWithOperationDocument->deadline,
                            'priority' => $getMediaWithOperationDocument->priority,
                            'status' => $getMediaWithOperationDocument->status,
                            'user_id_sender' => auth()->id(),
                            'user_id_receiver' => $getIdPreviousUser,
                            'media_id' => $getMediaDocument->id,
                            'message' => $getMediaWithOperationDocument->message,
                            'receive_mail_notification' => $getMediaWithOperationDocument->receive_mail_notification,
                            'operation_type' => $request->validationType,
                            'operation_state' => 'rejected',
                            'num_operation' => (string) Str::orderedUuid(),
                        ]);

                        $getOperations = Operation::where('media_id', $getMediaDocument->id)
                            ->where('user_id_receiver', auth()->id())
                            //->orWhere('user_id_sender', auth()->id())
                            ->get();

                        foreach ($getOperations as $operationList){
                            $operationList->update(['operation_state' => 'rejected']);
                        }
                    }
                //dd($getMediaDocument->operations->where('user_id_receiver', auth()->id())->toArray());

                $getLog = $getLog->where('operation_type', 'VALIDATE_DOCUMENT_REJECTED')->get();
                if(count($getLog) === 0){

                    $getDataNextUser = User::findOrFail($getIdPreviousUser);
                    self::trackOperations($getMediaDocument->id,
                        "VALIDATE_DOCUMENT_REJECTED",
                        $getDataNextUser->name .' a rejeté un document en attente ',
                        'pending',
                        auth()->id(),
                        $getIdPreviousUser,
                        auth()->user()->name,
                        $getMediaWithOperationDocument->message);

                }
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
    public function fillPDFFileSignature($file, $outputFilePath)
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

            //$left = 10;
            //$top = 10;
            //$text = "NiceSnippets.com";
            //$fpdi->Text($left,$top,$text);

            if($i==$count){
                $getSignature = Media::where('signed_by', auth()->id())->where('collection_name', 'signature')->first();
                $fpdi->Image($getSignature->getPath(), 130, 200, 40);
            }

            //$fpdi->SetXY(90, 50);
            //$fpdi->Write(10, "Bertin Mounok");

            //$fpdi->Image("file:///var/www/example-app/public/nice-logo.png", 40, 90);
        }

        return $fpdi->Output($outputFilePath, 'F');
    }


    public function fillPDFFileParaphe($file, $outputFilePath)
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

            //$left = 10;
            //$top = 10;
            //$text = "BERTIN MOUNOK";
            //$fpdi->Text($left,$top,$text);


            if($i<$count){
                $getSignature = Media::where('signed_by', auth()->id())->where('collection_name', 'paraphe')->first();
                $fpdi->Image($getSignature->getPath(), 10, 10, 40);
            }
            //$fpdi->SetXY(90, 50);
            //$fpdi->Write(10, "Bertin Mounok");

            //$fpdi->Image("file:///var/www/example-app/public/nice-logo.png", 40, 90);
        }

        return $fpdi->Output($outputFilePath, 'F');
    }


    function get_time_ago( $time )
    {
        $time_difference = time() - $time;

        if( $time_difference < 1 ) { return 'less than 1 second ago'; }
        $condition = array( 12 * 30 * 24 * 60 * 60 =>  'year',
            30 * 24 * 60 * 60       =>  'month',
            24 * 60 * 60            =>  'day',
            60 * 60                 =>  'hour',
            60                      =>  'minute',
            1                       =>  'second'
        );

        foreach( $condition as $secs => $str )
        {
            $d = $time_difference / $secs;

            if( $d >= 1 )
            {
                $t = round( $d );
                return 'about ' . $t . ' ' . $str . ( $t > 1 ? 's' : '' ) . ' ago';
            }
        }
    }
}
