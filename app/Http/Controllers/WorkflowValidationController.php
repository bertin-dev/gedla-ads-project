<?php

namespace App\Http\Controllers;

use App\Events\DocumentAdded;
use App\Http\Resources\Admin\UserResource;
use App\Models\AuditLog;
use App\Models\Folder;
use App\Models\Parapheur;
use App\Models\Project;
use App\Models\User;
use App\Models\ValidationStep;
use App\Notifications\sendEmailNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use Notification;

class WorkflowValidationController extends Controller
{

    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        abort_if(Gate::denies('workflow_management_access_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $projects = auth()->user()->projects->pluck('name', 'id')
            ->prepend(trans('global.pleaseSelect'), '');


        $users = User::where('id', '!=', auth()->id())
            ->whereHas('multiFolders.project')
            ->pluck('name', 'id')
            ->prepend(trans('global.pleaseSelect'), '');

        $children_level_n = Folder::with('project')
            ->whereHas('project.users', function($query) {
                $query->where('id', auth()->id());
            })
            ->whereNull('parent_id')
            ->with('subChildren')
            ->get();

        return view('front.workflow.create', compact('users', 'projects', 'children_level_n'));
    }

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
                        $this->templateForDocumentHistoric(ucfirst(auth()->user()->name) .' vient de démarrer le circuit de validation.'),
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
                        $this->templateForDocumentHistoric(ucfirst(auth()->user()->name) .' vient de démarrer le circuit de validation.'),
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

        return redirect()->back();
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
}
