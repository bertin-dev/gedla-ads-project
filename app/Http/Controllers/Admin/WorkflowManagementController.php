<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Folder;
use App\Models\Operation;
use App\Models\Parapheur;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
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
        //abort_if(Gate::denies('folder_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        //dd(Media::with('parapheur')->get()->toArray());
        return view('admin.workflow.index');
    }

    public function create()
    {
        //abort_if(Gate::denies('folder_access_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $projects = Project::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $users = User::where('id', '!=', \Auth::user()->id)
            ->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $folders = Folder::where('functionality', false)->get()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

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

        //loop on user array
        foreach ($request->workflow_user as $userItem):


            //step 1: check visibility of file
            if($request->visibility == 'private'){
                //parapheur
                $parapheur = Parapheur::where('user_id', $userItem)->first();
                if($parapheur == null){
                    $getLastInsertId = Parapheur::all()->max('id');
                    $parapheur = Parapheur::create([
                        'name' => 'parapheur'. $getLastInsertId + 1,
                        'project_id' => 1,
                        'user_id' => $userItem
                    ]);
                }

                foreach ($request->input('files', []) as $file) {
                    $media = $parapheur->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('files');
                    $media->created_by = \Auth::user()->id;
                    $media->parapheur_id = $parapheur->id;
                    $media->model_id = 0;
                    $media->save();
                    /*$media->update([
                        'created_by' => \Auth::user()->id,
                        'parapheur_id' => $parapheur->id
                    ]);*/

                    $operation = Operation::create([
                        'deadline' => $request->deadline,
                        'priority' => $request->priority,
                        'status' => $request->visibility,
                        'user_id_sender' => \Auth::user()->id,
                        'user_id_receiver' => $userItem,
                        'media_id' => $media->id,
                        'message' => $request->message,
                        'receive_mail_notification' => $request->boolean('flexCheckChecked'),
                        'operation_type' => 'create_workflow',
                        'operation_state' => 'pending',
                        'num_operation' => (string) Str::orderedUuid(),
                    ]);
                }

            }


        endforeach;


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
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = User::where('id', $id)->get();
        return response()->json($user);
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


    public function hasReadMedia(Request $request){

        Operation::where([
            ['media_id', $request->id],
            ['user_id_receiver', \Auth::user()->id]
        ])->first()->update([
            'receiver_read_doc' => true,
            'receiver_read_doc_at' => date('Y-m-d H:i:s', time())
        ]);

        //$infoUser = User::with(['receiveOperations', 'sendOperations', 'multiFolders'])->find(auth()->id());
        $infoMedia = Media::with(['parapheur', 'category', 'operations', 'createdBy', 'signedBy'])->find(1);
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
            'user' => $allUser
        ], Response::HTTP_OK);
    }


    public function validateDocument(Request $request){
        $getMediaDocument = Media::find($request->id);
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
