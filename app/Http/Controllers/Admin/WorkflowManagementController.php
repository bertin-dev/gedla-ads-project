<?php

namespace App\Http\Controllers\Admin;

use App\Events\DocumentAdded;
use App\Events\validationStepCompleted;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyFolderRequest;
use App\Http\Resources\Admin\UserResource;
use App\Models\AuditLog;
use App\Models\Folder;
use App\Models\Parapheur;
use App\Models\Project;
use App\Models\User;
use App\Models\Notification as Notifications;
use App\Models\ValidationStep;
use App\Notifications\sendEmailNotification;
use Illuminate\Database\Eloquent\Model;
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

    private $operationTypes = ["SEND_DOCUMENT", "VALIDATE_DOCUMENT", "VALIDATE_DOCUMENT_SIGNATURE",
        "SEND_DOCUMENT_SIGNATURE", "VALIDATE_DOCUMENT_PARAPHEUR", "SEND_DOCUMENT_PARAPHEUR", "OPEN_DOCUMENT", "PREVIEW_DOCUMENT",
        "START_VALIDATION", "REJECTED_DOCUMENT", "EDIT_DOCUMENT", "SAVE_DOCUMENT", "DOWNLOAD_DOCUMENT", "ARCHIVE_DOCUMENT", "IMPORT_DOCUMENT",
        "RESTORE_ARCHIVE_DOCUMENT"];

    public function __construct(){
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        abort_if(Gate::denies('workflow_validation_management_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $allMediaWithValidationStep = Media::with('validationSteps', 'createdBy', 'updatedBy', 'parapheur')->get();
        return view('admin.workflow.index', compact('allMediaWithValidationStep'));
    }

    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        abort_if(Gate::denies('workflow_management_access_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $projects = Project::all()->pluck('name', 'id')
            ->prepend(trans('global.pleaseSelect'), '');

        $users = User::where('id', '!=', auth()->id())
            ->whereHas('multiFolders')
            ->pluck('name', 'id')
            ->prepend(trans('global.pleaseSelect'), '');

        return view('admin.workflow.create', compact('users', 'projects'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    /*public function store(Request $request)
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
    }*/

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        //dd($request->all());
        $globalDeadline = Carbon::parse($request->global_deadline);
        $getUserListForWorkflowValidation = collect();
        $usersId = $request->user_list;
        //Get ID of first main user or delegate user
        //si userList[n] est différent de null alors nous avons une délégation de signature
        $firstUserId = $request->get("user_list".$usersId[0]) != null ? $request->get("user_list".$usersId[0]) : $usersId[0];
        //dd($request->get("user_list4"));
        for($i=0; $i<count($usersId); $i++){

            $smallDeadline = Carbon::parse($request->get("deadline".$usersId[$i]));

            if($globalDeadline->lessThan($smallDeadline)){
                //deadline global inférieur
                return back()->withInput($request->input())->withErrors(['errors' => 'Le deadline d\'un ou plusieurs utilisateurs est supérieur au deadline global du circuit de validation' ]);
            }
        }

        //dd($getUserListForWorkflowValidation->all());


        //step 1: check visibility of file
        if($request->visibility == 'private') {
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
                $media->global_deadline = $request->global_deadline;
                $media->visibility = $request->visibility;
                $media->save();

                foreach ($usersId as $key => $userId) {
                    $validationStep = new ValidationStep([
                        'media_id' => $media->id,
                        'user_id' => $userId,
                        'deadline' => $request->get("deadline". $userId),
                        'order' => $key,
                    ]);
                    $validationStep->save();
                }

                $getLog = AuditLog::where('media_id', $media->id)
                    ->where('operation_type', 'START_VALIDATION')
                    ->where('current_user_id', auth()->id())
                    ->get();
                if(count($getLog) === 0){
                    self::trackOperations($media->id,
                        "START_VALIDATION",
                        $this->templateForDocumentHistoric(ucfirst(auth()->user()->name) .' a démarré le circuit de validation.'),
                        'success',
                        auth()->id(),
                        $firstUserId,
                        auth()->user()->name,
                        $request->message
                    );
                }

                //SEND NOTIFICATION NEXT USER
                $details = [
                    'user' => User::findOrFail($firstUserId),
                    'subject' => 'Attente de validation',
                    'body' => 'Vous avez le document "' . strtoupper(substr($media->file_name, 14)) . '" en attente de validation.',
                    'media_id' => $media->id,
                    'media_name' => $media->file_name,
                    'validation_step_id' => 0,
                ];
                event(new DocumentAdded($details));

            }

            //if checkbox of email checked, then email is send at receiver
            if($request->boolean('flexCheckChecked')){
                $getUser = User::findOrFail($firstUserId);
                $details = [
                    'greeting' => 'Bonjour '. $getUser->name,
                    'body' => 'Vous avez réçu un message de '. ucfirst(auth()->user()->name) . ': ' .$request->message,
                    'actiontext' => 'Subscribe this channel',
                    'actionurl' => '/',
                    'lastline' => 'Nous vous remercions pour votre bonne comprehension.'
                ];
                Notification::send($getUser, new sendEmailNotification($details));
            }

        } else {

            //Get Folder
            $user = User::with('multiFolders')->findOrFail($firstUserId);
            $folder = $user->multiFolders->first();

            foreach ($request->input('files', []) as $file) {
                //Create Media table with new datas for receiver user
                $media = $folder->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('files');
                $media->version = $media->version + 1;
                $media->created_by = \Auth::user()->id;
                $media->global_deadline = $request->global_deadline;
                $media->visibility = $request->visibility;
                $media->save();

                foreach ($usersId as $key => $userId) {
                    $validationStep = new ValidationStep([
                        'media_id' => $media->id,
                        'user_id' => $userId,
                        'deadline' => $request->get("deadline". $userId),
                        'order' => $key,
                    ]);
                    $validationStep->save();
                }
                $getLog = AuditLog::where('media_id', $media->id)
                    ->where('operation_type', 'START_VALIDATION')
                    ->where('current_user_id', auth()->id())
                    ->get();
                if(count($getLog) === 0){
                    self::trackOperations($media->id,
                        "START_VALIDATION",
                        $this->templateForDocumentHistoric(ucfirst(auth()->user()->name) .' a démarré le circuit de validation.'),
                        'success',
                        auth()->id(),
                        $firstUserId,
                        auth()->user()->name,
                        $request->message);
                }

                //SEND NOTIFICATION NEXT USER
                $details = [
                    'user' => User::findOrFail($firstUserId),
                    'subject' => 'Attente de validation',
                    'body' => 'Vous avez le document "' . strtoupper(substr($media->file_name, 14)) . '" en attente de validation.',
                    'media_id' => $media->id,
                    'media_name' => $media->file_name,
                    'validation_step_id' => 0,
                ];
                event(new DocumentAdded($details));
            }
            //if checkbox of email checked, then email is send at receiver
            if($request->boolean('flexCheckChecked')){
                $getUser = User::findOrFail($firstUserId);
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

        return redirect()->route('admin.workflow-management.index');
    }


    public function storeMedia(Request $request): \Illuminate\Http\JsonResponse
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
    public function show($id): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        abort_if(Gate::denies('workflow_management_access_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $getMediaAndUser = ValidationStep::with('media', 'user')
            ->findOrFail($id);
        return view('admin.workflow.show', compact('getMediaAndUser'));
    }

    public function showUsers($id): UserResource
    {
        abort_if(Gate::denies('workflow_management_access_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $getUsersInProject = Project::with('users')
            ->findOrFail($id)
            ->users
            ->pluck('name', 'id')
            ->prepend(trans('global.pleaseSelect'), '');
        return new UserResource($getUsersInProject);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): \Illuminate\Http\Response
    {
        abort_if(Gate::denies('workflow_management_access_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        abort_if(Gate::denies('workflow_management_access_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        //Operation::where('id', $id)->delete();

        return back();
    }

    public function massDestroy(MassDestroyFolderRequest $request): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        //Operation::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function openDocument(Request $request): \Illuminate\Http\JsonResponse
    {
        $media = Media::findOrFail($request->id);

        $getLog = AuditLog::where('media_id', $request->id)
            ->where('current_user_id', auth()->id())
            ->where('operation_type', 'OPEN_DOCUMENT')
            ->get();

        if(count($getLog) === 0){
            self::trackOperations($request->id,
                "OPEN_DOCUMENT",
                $this->templateForDocumentHistoric(ucfirst(auth()->user()->name) .' a ouvert le document '. strtoupper($media->name)),
                'success',
                null,
                auth()->id(),
                auth()->user()->name,
                ucfirst(auth()->user()->name) .' a ouvert le document '. strtoupper($media->name));
        }
        return \response()->json($getLog);
    }

    public function previewDocument(Request $request): \Illuminate\Http\JsonResponse
    {


            //GET ALL LOGS OF MEDIA SPECIFIED AND AUTH USER
            $getCountLog = AuditLog::where('media_id', $request->id)
                ->where('current_user_id', auth()->id())
                ->where('operation_type', "PREVIEW_DOCUMENT")
                ->get();

            if(count($getCountLog) === 0){

                self::trackOperations($request->id,
                    "PREVIEW_DOCUMENT",
                    $this->templateForDocumentHistoric(ucfirst(auth()->user()->name) .' a prévisualisé le document '. strtoupper($request->name)),
                    'success',
                    null,
                    auth()->id(),
                    auth()->user()->name,
                    ucfirst(auth()->user()->name) .' a prévisualisé le document '. strtoupper($request->name),
                    '',
                    date('Y-m-d H:i:s', time()),
                );
            }


        $getAllLog = AuditLog::where('media_id', $request->id)
            ->whereIn('operation_type', $this->operationTypes)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'tracking' => $getAllLog,
        ], Response::HTTP_OK);
    }

    public function validateDocument(Request $request): \Illuminate\Http\JsonResponse
    {
        $getMediaDocument = Media::findOrFail($request->id);
        $success = "";
        $error = "";

        $validationStep = $getMediaDocument
            ->validationSteps()
            ->where('user_id', auth()->id())
            ->where('statut', 0)
            ->first();

        switch ($request->validationType){
            case "validation":

                if($getMediaDocument->visibility == "public") {
                    if ($validationStep) {

                        $nextStepValidation = $getMediaDocument
                            ->validationSteps()
                            ->where('order', '>', $validationStep->order)
                            ->where('statut', 0)
                            ->first();

                        $validationStep->statut = 1;
                        $validationStep->save();

                        if ($nextStepValidation) {
                            $nextUser = $nextStepValidation->user;
                            /*
                            // Vérifier s'il y a des erreurs lors de l'enregistrement
                            if ($nextStepValidation->save() === false) {
                                var_dump($nextStepValidation->getErrors()); // Afficher les erreurs
                            }*/

                            //SEND NOTIFICATION NEXT USER
                            $detailsMedia = [
                                'user' => $nextUser,
                                'subject' => 'Attente de validation',
                                'body' => 'Vous avez le document "' . strtoupper(substr($getMediaDocument->file_name, 14)) . '" en attente de validation.',
                                'media_id' => $getMediaDocument->id,
                                'media_name' => $getMediaDocument->file_name,
                                'validation_step_id' => $nextStepValidation->order,
                            ];
                            event(new DocumentAdded($detailsMedia));

                            //GET FOLDER AND UPDATE MEDIA TABLE
                            $user = User::with('multiFolders')->where('id', $nextUser->id)->first();
                            $folder = $user->multiFolders->first();
                            //update media table
                            $getMediaDocument->version = $getMediaDocument->version + 1;
                            $getMediaDocument->model_id = $folder->id;
                            $getMediaDocument->save();

                            //SAVE OPERATION IN LOG
                            $getLog = AuditLog::where('media_id', $getMediaDocument->id)
                                ->where('operation_type', 'VALIDATE_DOCUMENT')
                                ->where('current_user_id', auth()->id())
                                ->get();
                            if(count($getLog) === 0){
                                self::trackOperations($request->id,
                                    "VALIDATE_DOCUMENT",
                                    $this->templateForDocumentHistoric(ucfirst(auth()->user()->name) .' a validé le document '. strtoupper(substr($getMediaDocument->name, 14))),
                                    'success',
                                    null,
                                    auth()->id(),
                                    auth()->user()->name,
                                    ucfirst(auth()->user()->name) .' a validé le document '. strtoupper(substr($getMediaDocument->name, 14)),
                                );
                            }

                            $success = 'la validation du document '.strtoupper(substr($getMediaDocument->file_name, 14)).' a été effectué avec succès et une notification a été envoyé à ' . ucfirst($nextUser->name);
                        }
                        else{
                            $getMediaDocument->statut = 1;
                            $getMediaDocument->save();

                            $creator_user = $getMediaDocument->createdBy;

                            //SEND NOTIFICATION NEXT USER
                            $detailsMedia = [
                                'user' => $creator_user,
                                'subject' => 'Circuit de validation terminé',
                                'body' => 'La dernière étape du circuit de validation du document "' . strtoupper(substr($validationStep->media->name, 14)) . '" est terminé.',
                                'media_id' => $getMediaDocument->id,
                                'media_name' => strtoupper(substr($getMediaDocument->file_name, 14)),
                                'validation_step_id' => $validationStep->order ?? 0,
                            ];
                            event(new validationStepCompleted($detailsMedia));
                            $error = 'Toutes les étapes du circuit de validation ont déjà été effectuées';
                        }

                    }
                    else {
                        $error = "Vous ne pouvez pas valider le document " . strtoupper(substr($getMediaDocument->file_name, 14));
                    }
                }
                else{
                    if ($validationStep) {

                        $nextStepValidation = $getMediaDocument
                            ->validationSteps()
                            ->where('order', '>', $validationStep->order)
                            ->where('statut', 0)
                            ->first();

                        $validationStep->statut = 1;
                        $validationStep->save();

                        //dd($nextStepValidation->toArray());
                        if ($nextStepValidation) {
                            $nextUser = $nextStepValidation->user;
                            /*
                            // Vérifier s'il y a des erreurs lors de l'enregistrement
                            if ($nextStepValidation->save() === false) {
                                var_dump($nextStepValidation->getErrors()); // Afficher les erreurs
                            }*/

                            //SEND NOTIFICATION NEXT USER
                            $detailsMedia = [
                                'user' => $nextUser,
                                'subject' => 'Attente de validation',
                                'body' => 'Vous avez le document "' . strtoupper(substr($getMediaDocument->file_name, 14)) . '" en attente de validation.',
                                'media_id' => $getMediaDocument->id,
                                'media_name' => $getMediaDocument->file_name,
                                'validation_step_id' => $nextStepValidation->order,
                            ];
                            event(new DocumentAdded($detailsMedia));

                            $parapheur = Parapheur::where('user_id', $nextUser->id)->first();
                            if($parapheur == null){
                                $getLastInsertId = Parapheur::all()->max('id');
                                $parapheur = Parapheur::create([
                                    'name' => 'parapheur'. $getLastInsertId + 1,
                                    'project_id' => 1,
                                    'user_id' => $nextUser->id
                                ]);
                            }

                            //update media table
                            $getMediaDocument->version = $getMediaDocument->version + 1;
                            $getMediaDocument->parapheur_id = $parapheur->id;
                            $getMediaDocument->save();


                            //SAVE OPERATION IN LOG
                            $getLog = AuditLog::where('media_id', $getMediaDocument->id)
                                ->where('operation_type', 'VALIDATE_DOCUMENT')
                                ->where('current_user_id', auth()->id())
                                ->get();
                            if(count($getLog) === 0){
                                self::trackOperations($request->id,
                                    "VALIDATE_DOCUMENT",
                                    $this->templateForDocumentHistoric(ucfirst(auth()->user()->name) .' a validé le document '. strtoupper(substr($getMediaDocument->name, 14))),
                                    'success',
                                    null,
                                    auth()->id(),
                                    auth()->user()->name,
                                    ucfirst(auth()->user()->name) .' a validé le document '. strtoupper(substr($getMediaDocument->name, 14)),
                                );
                            }
                            $success = 'La validation du document ' .strtoupper(substr($getMediaDocument->file_name, 14)). ' a été effectué avec succès et une notification a été envoyé à '. ucfirst($nextUser->name);
                        }
                        else{
                            $getMediaDocument->statut = 1;
                            $getMediaDocument->save();

                            $creator_user = $getMediaDocument->createdBy;

                            //SEND NOTIFICATION NEXT USER
                            $detailsMedia = [
                                'user' => $creator_user,
                                'subject' => 'Circuit de validation terminé',
                                'body' => 'La dernière étape du circuit de validation du document "' . strtoupper(substr($validationStep->media->name, 14)) . '" est terminé.',
                                'media_id' => $getMediaDocument->id,
                                'media_name' => strtoupper(substr($getMediaDocument->file_name, 14)),
                                'validation_step_id' => $validationStep->order ?? 0,
                            ];
                            event(new validationStepCompleted($detailsMedia));
                            $error = 'Toutes les étapes du circuit de validation ont déjà été effectuées';
                        }

                    } else {
                        $error = "Vous ne pouvez pas valider le document " .strtoupper(substr($getMediaDocument->file_name, 14));
                    }
                }
                break;

            case "rejected":

                if($getMediaDocument->visibility == "public"){
                    if ($validationStep) {
                        $validationStep->statut = -1;
                        $validationStep->save();

                        $getMediaDocument->statut = -1;
                        $getMediaDocument->save();

                        $previousStepValidation = $getMediaDocument
                            ->validationSteps()
                            ->where('order', '=', ($validationStep->order - 1))
                            ->where('statut', 1)
                            ->first();

                        if ($previousStepValidation) {
                            $previousUser = $previousStepValidation->user;
                            $user = User::with('multiFolders')->where('id', $previousUser->id)->first();
                            $folder = $user->multiFolders->first();


                            //SEND NOTIFICATION NEXT USER
                            $detailsMedia = [
                                'user' => $previousUser,
                                'subject' => 'validation rejeté',
                                'body' => 'Vous avez le document "' . strtoupper(substr($getMediaDocument->file_name, 14)) . '" qui a été rejecté par ' . ucfirst($previousUser->name),
                                'media_id' => $getMediaDocument->id,
                                'media_name' => $getMediaDocument->file_name,
                                'validation_step_id' => $validationStep->order,
                            ];
                            event(new DocumentAdded($detailsMedia));


                            //update media table
                            $getMediaDocument->version = $getMediaDocument->version + 1;
                            $getMediaDocument->model_id = $folder->id;
                            $getMediaDocument->save();

                            //SAVE OPERATION IN LOG
                            $getLog = AuditLog::where('media_id', $getMediaDocument->id)
                                ->where('operation_type', 'REJECTED_DOCUMENT')
                                ->where('current_user_id', auth()->id())
                                ->get();
                            if(count($getLog) === 0){
                                self::trackOperations($request->id,
                                    "REJECTED_DOCUMENT",
                                    $this->templateForDocumentHistoric(ucfirst(auth()->user()->name) .' à rejeté le document '. strtoupper(substr($getMediaDocument->name, 14))),
                                    'success',
                                    null,
                                    auth()->id(),
                                    auth()->user()->name,
                                    ucfirst(auth()->user()->name) .' a rejeté le document '. strtoupper(substr($getMediaDocument->name, 14)),
                                );
                            }
                        }

                        $success = "Le document ".strtoupper(substr($getMediaDocument->file_name, 14))." a été rejeté avec succès";
                    }
                    else {
                        $error = "Vous ne pouvez pas Rejeter le document " . strtoupper(substr($getMediaDocument->file_name, 14));
                    }
                }
                else{
                    if ($validationStep) {
                        $validationStep->statut = -1;
                        $validationStep->save();

                        $getMediaDocument->statut = -1;
                        $getMediaDocument->save();

                        $previousStepValidation = $getMediaDocument
                            ->validationSteps()
                            ->where('order', '=', ($validationStep->order - 1))
                            ->where('statut', 1)
                            ->first();

                        if ($previousStepValidation) {
                            $previousUser = $previousStepValidation->user;

                            //SEND NOTIFICATION NEXT USER
                            $detailsMedia = [
                                'user' => $previousUser,
                                'subject' => 'validation rejeté',
                                'body' => 'Vous avez le document "' . strtoupper(substr($getMediaDocument->file_name, 14)) . '" qui a été rejecté par ' . ucfirst($previousUser->name),
                                'media_id' => $getMediaDocument->id,
                                'media_name' => $getMediaDocument->file_name,
                                'validation_step_id' => $validationStep->order,
                            ];
                            event(new DocumentAdded($detailsMedia));

                            $parapheur = Parapheur::where('user_id', $previousUser->id)->first();
                            if($parapheur == null){
                                $getLastInsertId = Parapheur::all()->max('id');
                                $parapheur = Parapheur::create([
                                    'name' => 'parapheur'. $getLastInsertId + 1,
                                    'project_id' => 1,
                                    'user_id' => $previousUser->id
                                ]);
                            }

                            //update media table
                            $getMediaDocument->version = $getMediaDocument->version + 1;
                            $getMediaDocument->parapheur_id = $parapheur->id;
                            $getMediaDocument->save();

                            //SAVE OPERATION IN LOG
                            $getLog = AuditLog::where('media_id', $getMediaDocument->id)
                                ->where('operation_type', 'REJECTED_DOCUMENT')
                                ->where('current_user_id', auth()->id())
                                ->get();
                            if(count($getLog) === 0){
                                self::trackOperations($request->id,
                                    "REJECTED_DOCUMENT",
                                    $this->templateForDocumentHistoric(ucfirst(auth()->user()->name) .' a rejeté le document '. strtoupper(substr($getMediaDocument->name, 14))),
                                    'success',
                                    null,
                                    auth()->id(),
                                    auth()->user()->name,
                                    ucfirst(auth()->user()->name) .' a rejeté le document '. strtoupper(substr($getMediaDocument->name, 14)),
                                );
                            }
                        }

                        $success = "Le document ".strtoupper(substr($getMediaDocument->file_name, 14))." a été rejeté avec succès";
                    }
                    else {
                        $error = "Vous ne pouvez pas Rejeter le document " . strtoupper(substr($getMediaDocument->file_name, 14));
                    }
                }
                break;


            case "validation_signature":
                if($getMediaDocument->visibility == "public") {
                    if ($validationStep) {

                        $nextStepValidation = $getMediaDocument
                            ->validationSteps()
                            ->where('order', '>', $validationStep->order)
                            ->where('statut', 0)
                            ->first();

                        $validationStep->statut = 1;
                        $validationStep->save();

                        if ($nextStepValidation) {
                            $nextUser = $nextStepValidation->user;
                            /*
                            // Vérifier s'il y a des erreurs lors de l'enregistrement
                            if ($nextStepValidation->save() === false) {
                                var_dump($nextStepValidation->getErrors()); // Afficher les erreurs
                            }*/

                            //SEND NOTIFICATION NEXT USER
                            $detailsMedia = [
                                'user' => $nextUser,
                                'subject' => 'Document en attente',
                                'body' => 'Vous avez le document "' . strtoupper(substr($getMediaDocument->file_name, 14)) . '" en attente de validation.',
                                'media_id' => $getMediaDocument->id,
                                'media_name' => $getMediaDocument->file_name,
                                'validation_step_id' => $nextStepValidation->order,
                            ];
                            event(new DocumentAdded($detailsMedia));

                            //GET FOLDER AND UPDATE MEDIA TABLE
                            $user = User::with('multiFolders')->where('id', $nextUser->id)->first();
                            $folder = $user->multiFolders->first();
                            //update media table
                            $getMediaDocument->version = $getMediaDocument->version + 1;
                            $getMediaDocument->model_id = $folder->id;
                            $getMediaDocument->save();

                            //SAVE OPERATION IN LOG
                            $getLog = AuditLog::where('media_id', $getMediaDocument->id)
                                ->where('operation_type', 'VALIDATE_DOCUMENT')
                                ->where('current_user_id', auth()->id())
                                ->get();
                            if(count($getLog) === 0){
                                self::trackOperations($request->id,
                                    "VALIDATE_DOCUMENT",
                                    $this->templateForDocumentHistoric(ucfirst(auth()->user()->name) . ' a validé le document ' . strtoupper(substr($getMediaDocument->name, 14))),
                                    'success',
                                    null,
                                    auth()->id(),
                                    auth()->user()->name,
                                    ucfirst(auth()->user()->name) . ' a validé le document ' . strtoupper(substr($getMediaDocument->name, 14)),
                                );
                            }

                            $signatureWithValidation = $getMediaDocument->validationSteps();

                            $totalSignatureRemaning = $signatureWithValidation
                                ->where('order', '>', $validationStep->order)
                                ->where('statut', 0)
                                ->count();

                            $totalSignatureHasValidate = $signatureWithValidation
                                ->where('order', '>', $validationStep->order)
                                ->count();

                            $position = ($totalSignatureHasValidate - $totalSignatureRemaning) + 1;
                            //SIGN DOCUMENT
                            $filePath = $getMediaDocument->getPath();
                            $getSignature = Media::where('signed_by', auth()->id())
                                ->whereIn('collection_name', ['signature'])
                                ->first();
                            $this->addSignatureToPDF($filePath, $getSignature->getPath(), auth()->user()->name, $totalSignatureHasValidate, $position);
                            $success = 'la validation du document ' . strtoupper(substr($getMediaDocument->file_name, 14)) . ' a été effectué avec succès et une notification a été envoyé à ' . ucfirst($nextUser->name);
                        }
                        else{
                            $getMediaDocument->statut = 1;
                            $getMediaDocument->save();

                            $creator_user = $getMediaDocument->createdBy;

                            //SEND NOTIFICATION NEXT USER
                            $detailsMedia = [
                                'user' => $creator_user,
                                'subject' => 'Circuit de validation terminé',
                                'body' => 'La dernière étape du circuit de validation du document "' . strtoupper(substr($validationStep->media->name, 14)) . '" est terminé.',
                                'media_id' => $getMediaDocument->id,
                                'media_name' => strtoupper(substr($getMediaDocument->file_name, 14)),
                                'validation_step_id' => $validationStep->order ?? 0,
                            ];
                            event(new validationStepCompleted($detailsMedia));
                            $error = 'Toutes les étapes du circuit de validation ont déjà été effectuées';
                        }

                    }
                    else {
                        $error = "Vous ne pouvez pas valider le document " . strtoupper(substr($getMediaDocument->file_name, 14));
                    }
                }
                else{
                    if ($validationStep) {

                        $nextStepValidation = $getMediaDocument
                            ->validationSteps()
                            ->where('order', '>', $validationStep->order)
                            ->where('statut', 0)
                            ->first();

                        $validationStep->statut = 1;
                        $validationStep->save();

                        //dd($nextStepValidation->toArray());
                        if ($nextStepValidation) {
                            $nextUser = $nextStepValidation->user;
                            /*
                            // Vérifier s'il y a des erreurs lors de l'enregistrement
                            if ($nextStepValidation->save() === false) {
                                var_dump($nextStepValidation->getErrors()); // Afficher les erreurs
                            }*/

                            //SEND NOTIFICATION NEXT USER
                            $detailsMedia = [
                                'user' => $nextUser,
                                'subject' => 'Attente de validation',
                                'body' => 'Vous avez le document "' . strtoupper(substr($getMediaDocument->file_name, 14)) . '" en attente de validation.',
                                'media_id' => $getMediaDocument->id,
                                'media_name' => $getMediaDocument->file_name,
                                'validation_step_id' => $nextStepValidation->order,
                            ];
                            event(new DocumentAdded($detailsMedia));

                            $parapheur = Parapheur::where('user_id', $nextUser->id)->first();
                            if($parapheur == null){
                                $getLastInsertId = Parapheur::all()->max('id');
                                $parapheur = Parapheur::create([
                                    'name' => 'parapheur'. $getLastInsertId + 1,
                                    'project_id' => 1,
                                    'user_id' => $nextUser->id
                                ]);
                            }

                            //update media table
                            $getMediaDocument->version = $getMediaDocument->version + 1;
                            $getMediaDocument->parapheur_id = $parapheur->id;
                            $getMediaDocument->save();


                            //SAVE OPERATION IN LOG
                            $getLog = AuditLog::where('media_id', $getMediaDocument->id)
                                ->where('operation_type', 'VALIDATE_DOCUMENT')
                                ->where('current_user_id', auth()->id())
                                ->get();
                            if(count($getLog) === 0){
                                self::trackOperations($request->id,
                                    "VALIDATE_DOCUMENT",
                                    $this->templateForDocumentHistoric(ucfirst(auth()->user()->name) . ' a validé le document ' . strtoupper(substr($getMediaDocument->name, 14))),
                                    'success',
                                    null,
                                    auth()->id(),
                                    auth()->user()->name,
                                    ucfirst(auth()->user()->name) . ' a validé le document ' . strtoupper(substr($getMediaDocument->name, 14)),
                                );
                            }

                            $signatureWithValidation = $getMediaDocument->validationSteps();

                            $totalSignatureRemaning = $signatureWithValidation
                                ->where('order', '>', $validationStep->order)
                                ->where('statut', 0)
                                ->count();

                            $totalSignatureHasValidate = $signatureWithValidation
                                ->where('order', '>', $validationStep->order)
                                ->count();

                            $position = ($totalSignatureHasValidate - $totalSignatureRemaning) + 1;
                            //SIGN DOCUMENT
                            $filePath = $getMediaDocument->getPath();
                            $getSignature = Media::where('signed_by', auth()->id())
                                ->whereIn('collection_name', ['signature'])
                                ->first();
                            $this->addSignatureToPDF($filePath, $getSignature->getPath(), auth()->user()->name, $totalSignatureHasValidate, $position);

                            $success = 'La validation du document ' . strtoupper(substr($getMediaDocument->file_name, 14)) . ' a été effectué avec succès et une notification a été envoyé à ' . ucfirst($nextUser->name);
                        }
                        else{
                            $getMediaDocument->statut = 1;
                            $getMediaDocument->save();

                            $creator_user = $getMediaDocument->createdBy;

                            //SEND NOTIFICATION NEXT USER
                            $detailsMedia = [
                                'user' => $creator_user,
                                'subject' => 'Circuit de validation terminé',
                                'body' => 'La dernière étape du circuit de validation du document "' . strtoupper(substr($validationStep->media->name, 14)) . '" est terminé.',
                                'media_id' => $getMediaDocument->id,
                                'media_name' => strtoupper(substr($getMediaDocument->file_name, 14)),
                                'validation_step_id' => $validationStep->order ?? 0,
                            ];
                            event(new validationStepCompleted($detailsMedia));
                            $error = 'Toutes les étapes du circuit de validation ont déjà été effectuées';
                        }

                    } else {
                        $error = "Vous ne pouvez pas valider le document " .strtoupper(substr($getMediaDocument->file_name, 14));
                    }
                }
                break;

            case "validation_paraphe":
                if($getMediaDocument->visibility == "public") {
                    if ($validationStep) {
                        $nextStepValidation = $getMediaDocument
                            ->validationSteps()
                            ->where('order', '>', $validationStep->order)
                            ->where('statut', 0)
                            ->first();

                        $validationStep->statut = 1;
                        $validationStep->save();

                        if ($nextStepValidation) {
                            $nextUser = $nextStepValidation->user;
                            /*
                            // Vérifier s'il y a des erreurs lors de l'enregistrement
                            if ($nextStepValidation->save() === false) {
                                var_dump($nextStepValidation->getErrors()); // Afficher les erreurs
                            }*/

                            //SEND NOTIFICATION NEXT USER
                            $detailsMedia = [
                                'user' => $nextUser,
                                'subject' => 'Attente de validation',
                                'body' => 'Vous avez le document "' . strtoupper(substr($getMediaDocument->file_name, 14)) . '" en attente de validation.',
                                'media_id' => $getMediaDocument->id,
                                'media_name' => $getMediaDocument->file_name,
                                'validation_step_id' => $nextStepValidation->order,
                            ];
                            event(new DocumentAdded($detailsMedia));

                            //GET FOLDER AND UPDATE MEDIA TABLE
                            $user = User::with('multiFolders')->where('id', $nextUser->id)->first();
                            $folder = $user->multiFolders->first();
                            //update media table
                            $getMediaDocument->version = $getMediaDocument->version + 1;
                            $getMediaDocument->model_id = $folder->id;
                            $getMediaDocument->save();

                            //SAVE OPERATION IN LOG
                            $getLog = AuditLog::where('media_id', $getMediaDocument->id)
                                ->where('operation_type', 'VALIDATE_DOCUMENT')
                                ->where('current_user_id', auth()->id())
                                ->get();
                            if(count($getLog) === 0){
                                self::trackOperations($request->id,
                                    "VALIDATE_DOCUMENT",
                                    $this->templateForDocumentHistoric(ucfirst(auth()->user()->name) . ' validé le document ' . strtoupper(substr($getMediaDocument->name, 14))),
                                    'success',
                                    null,
                                    auth()->id(),
                                    auth()->user()->name,
                                    ucfirst(auth()->user()->name) . ' a validé le document ' . strtoupper(substr($getMediaDocument->name, 14)),
                                );
                            }

                            //PARAPH DOCUMENT
                            $signatureWithValidation = $getMediaDocument->validationSteps();

                            $totalSignatureRemaning = $signatureWithValidation
                                ->where('order', '>', $validationStep->order)
                                ->where('statut', 0)
                                ->count();

                            $totalSignatureHasValidate = $signatureWithValidation
                                ->where('order', '>', $validationStep->order)
                                ->count();

                            $position = ($totalSignatureHasValidate - $totalSignatureRemaning) + 1;
                            $filePath = $getMediaDocument->getPath();

                            $getInitial = Media::where('signed_by', auth()->id())
                                ->whereIn('collection_name', ['paraphe'])
                                ->first();

                            $this->addInitialToPDF($filePath, $getInitial->getPath(), auth()->user()->name, $totalSignatureHasValidate, $position);

                            $success = 'la validation du document ' . strtoupper(substr($getMediaDocument->file_name, 14)) . ' a été effectué avec succès et une notification a été envoyé à ' . ucfirst($nextUser->name);
                        }
                        else{
                            $getMediaDocument->statut = 1;
                            $getMediaDocument->save();

                            $creator_user = $getMediaDocument->createdBy;

                            //SEND NOTIFICATION NEXT USER
                            $detailsMedia = [
                                'user' => $creator_user,
                                'subject' => 'Circuit de validation terminé',
                                'body' => 'La dernière étape du circuit de validation du document "' . strtoupper(substr($validationStep->media->name, 14)) . '" est terminé.',
                                'media_id' => $getMediaDocument->id,
                                'media_name' => strtoupper(substr($getMediaDocument->file_name, 14)),
                                'validation_step_id' => $validationStep->order ?? 0,
                            ];
                            event(new validationStepCompleted($detailsMedia));
                            $error = 'Toutes les étapes du circuit de validation ont déjà été effectuées';
                        }

                    }
                    else {
                        $error = "Vous ne pouvez pas valider le document " . strtoupper(substr($getMediaDocument->file_name, 14));
                    }
                }
                else{
                    if ($validationStep) {

                        $nextStepValidation = $getMediaDocument
                            ->validationSteps()
                            ->where('order', '>', $validationStep->order)
                            ->where('statut', 0)
                            ->first();

                        $validationStep->statut = 1;
                        $validationStep->save();

                        //dd($nextStepValidation->toArray());
                        if ($nextStepValidation) {
                            $nextUser = $nextStepValidation->user;
                            /*
                            // Vérifier s'il y a des erreurs lors de l'enregistrement
                            if ($nextStepValidation->save() === false) {
                                var_dump($nextStepValidation->getErrors()); // Afficher les erreurs
                            }*/

                            //SEND NOTIFICATION NEXT USER
                            $detailsMedia = [
                                'user' => $nextUser,
                                'subject' => 'Attente de validation',
                                'body' => 'Vous avez le document "' . strtoupper(substr($getMediaDocument->file_name, 14)) . '" en attente de validation.',
                                'media_id' => $getMediaDocument->id,
                                'media_name' => $getMediaDocument->file_name,
                                'validation_step_id' => $nextStepValidation->order,
                            ];
                            event(new DocumentAdded($detailsMedia));

                            $parapheur = Parapheur::where('user_id', $nextUser->id)->first();
                            if($parapheur == null){
                                $getLastInsertId = Parapheur::all()->max('id');
                                $parapheur = Parapheur::create([
                                    'name' => 'parapheur'. $getLastInsertId + 1,
                                    'project_id' => 1,
                                    'user_id' => $nextUser->id
                                ]);
                            }

                            //update media table
                            $getMediaDocument->version = $getMediaDocument->version + 1;
                            $getMediaDocument->parapheur_id = $parapheur->id;
                            $getMediaDocument->save();


                            //SAVE OPERATION IN LOG
                            $getLog = AuditLog::where('media_id', $getMediaDocument->id)
                                ->where('operation_type', 'VALIDATE_DOCUMENT')
                                ->where('current_user_id', auth()->id())
                                ->get();
                            if(count($getLog) === 0){
                                self::trackOperations($request->id,
                                    "VALIDATE_DOCUMENT",
                                    $this->templateForDocumentHistoric(ucfirst(auth()->user()->name) . ' a validé le document ' . strtoupper(substr($getMediaDocument->name, 14))),
                                    'success',
                                    null,
                                    auth()->id(),
                                    auth()->user()->name,
                                    ucfirst(auth()->user()->name) . ' a validé le document ' . strtoupper(substr($getMediaDocument->name, 14)),
                                );
                            }

                            //PARAPH DOCUMENT
                            $signatureWithValidation = $getMediaDocument->validationSteps();

                            $totalSignatureRemaning = $signatureWithValidation
                                ->where('order', '>', $validationStep->order)
                                ->where('statut', 0)
                                ->count();

                            $totalSignatureHasValidate = $signatureWithValidation
                                ->where('order', '>', $validationStep->order)
                                ->count();

                            $position = ($totalSignatureHasValidate - $totalSignatureRemaning) + 1;
                            $filePath = $getMediaDocument->getPath();
                            $getSignature = Media::where('signed_by', auth()->id())
                                ->whereIn('collection_name', ['paraphe'])
                                ->first();
                            $this->addInitialToPDF($filePath, $getSignature->getPath(), auth()->user()->name, $totalSignatureHasValidate, $position);

                            $success = 'La validation du document ' . strtoupper(substr($getMediaDocument->file_name, 14)) . ' a été effectué avec succès et une notification a été envoyé à ' . ucfirst($nextUser->name);
                        }
                        else{
                            $getMediaDocument->statut = 1;
                            $getMediaDocument->save();

                            $creator_user = $getMediaDocument->createdBy;

                            //SEND NOTIFICATION NEXT USER
                            $detailsMedia = [
                                'user' => $creator_user,
                                'subject' => 'Circuit de validation terminé',
                                'body' => 'La dernière étape du circuit de validation du document "' . strtoupper(substr($validationStep->media->name, 14)) . '" est terminé.',
                                'media_id' => $getMediaDocument->id,
                                'media_name' => strtoupper(substr($getMediaDocument->file_name, 14)),
                                'validation_step_id' => $validationStep->order ?? 0,
                            ];
                            event(new validationStepCompleted($detailsMedia));
                            $error = 'Toutes les étapes du circuit de validation ont déjà été effectuées';
                        }

                    } else {
                        $error = "Vous ne pouvez pas valider le document " .strtoupper(substr($getMediaDocument->file_name, 14));
                    }
                }
                break;
        }

        return response()->json([
            'success' => $success,
            'error' => $error
        ]);

    }

    public function downloadDocument(Request $request){
        $media = Media::findOrFail($request->id);

        $getLog = AuditLog::where('media_id', $media->id)
            ->where('current_user_id', auth()->id())
            ->where('operation_type', 'DOWNLOAD_DOCUMENT')
            ->get();
        if(count($getLog) === 0){
            self::trackOperations($media->id,
                "DOWNLOAD_DOCUMENT",
                $this->templateForDocumentHistoric(ucfirst(auth()->user()->name) .' a téléchargé le document '. strtoupper(substr($media->file_name, 14))),
                'success',
                null,
                auth()->id(),
                '',
                ucfirst(auth()->user()->name) .' a téléchargé le document '. strtoupper(substr($media->file_name, 14)));
        }

        return response()->download($media->getPath(), $media->file_name);
    }

    /*public function validateDocument(Request $request){
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

    }*/

    public function addSignatureToPDF($pdfFile, $signatureFile, $name, $numberOfSignatures, $position)
    {
        $pdf = new FPDI();

        // Importer le modèle PDF
        $pageCount = $pdf->setSourceFile($pdfFile);

        // Ajouter la signature à chaque page
        for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
            $template = $pdf->importPage($pageNumber);
            $size = $pdf->getTemplateSize($template);
            $pdf->AddPage($size['orientation'], array($size['width'], $size['height']));
            $pdf->useTemplate($template);
            $pdf->SetFont("helvetica", "", 15);

            // Ajouter la signature à la page courante
            $this->addSignatureToPage($pdf, $size, $signatureFile, $name, $numberOfSignatures, $position);
            // Enregistrer la page modifiée dans le fichier de sortie
            if ($pageNumber == $pageCount) {
                $pdf->Output($pdfFile, 'F');
            }
        }
    }

    private function addSignatureToPage($pdf, $size, $signatureFile, $name, $numberOfSignatures, $position)
    {
        // Ajouter la signature en fonction du nombre de signatures souhaitées
        if ($numberOfSignatures == 1) {
            $x = $size['width'] / 2;
            $y = $size['height'] - 100;
            $pdf->Image($signatureFile, $x, $y, 120);
            $pdf->SetXY($x + 40, $y + 10);
            $pdf->Write(10, $name);
        } else {
            //for ($i = 1; $i <= $numberOfSignatures; $i++) {
            $x = ($position - 1) * $size['width'] / $numberOfSignatures + $size['width'] / ($numberOfSignatures * 2);
            $y = $size['height'] - 100;
            $pdf->Image($signatureFile, $x, $y, 120);
            $pdf->SetXY($x + 40, $y + 10);
            $pdf->Write(10, $name);
            //break;
            //}
        }
    }


    public function addInitialToPDF($pdfFile, $signatureFile, $name, $numberOfSignatures, $position)
    {
        $pdf = new FPDI();

        // Importer le modèle PDF
        $pageCount = $pdf->setSourceFile($pdfFile);

        // Ajouter la signature à chaque page
        for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
            $template = $pdf->importPage($pageNumber);
            $size = $pdf->getTemplateSize($template);
            $pdf->AddPage($size['orientation'], array($size['width'], $size['height']));
            $pdf->useTemplate($template);
            $pdf->SetFont("helvetica", "", 15);

            // Ajouter le paraphe à la page courante
            $this->addInitialToPage($pdf, $size, $signatureFile, $name, $numberOfSignatures, $position);
            // Enregistrer la page modifiée dans le fichier de sortie
            if ($pageNumber == $pageCount) {
                $pdf->Output($pdfFile, 'F');
            }
        }
    }

    private function addInitialToPage($pdf, $size, $signatureFile, $name, $numberOfSignatures, $position)
    {
        $x = ($position - 1) * $size['width'] / $numberOfSignatures + $size['width'] / ($numberOfSignatures * 4);
        $y = $size['height'] - 290;
        $pdf->Image($signatureFile, $x, $y, 20);
        $pdf->SetXY($x + 10, $y + 10);
        $pdf->Write(10, $name);
    }


    public function templateForDocumentHistoric($params = '')
    {
        return '<div class="row schedule-item>
                <div class="col-md-2">
                <time class="timeago">Le ' . date('d-m-Y à H:i:s', time()) . '</time>
                </div>
                <div class="col-md-12">
                <p>' . $params . '</p>
                </div>
                </div>';
    }
}
