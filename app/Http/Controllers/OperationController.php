<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOperationRequest;
use App\Models\Folder;
use App\Models\Operation;
use App\Models\Parapheur;
use App\Notifications\sendEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Notification;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use \App\Models\User;

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

        //return view('front.folders.show_files');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreOperationRequest $request)
    {
        //get Media
        $mediaData = Media::find($request->media_id);

        //if data come from parapheur or folder
        if(isset($request->parapheur_id) AND !empty($request->parapheur_id))
        {
            if($request->visibility=="private"){
                //Get Parapheur
                $modelItem = Parapheur::find($mediaData->parapheur_id);

                //Get if user media assign has parapheur
                $getParapheurUserAssign = Parapheur::where('user_id' , $request->user_assign)->first();
                if($getParapheurUserAssign == null){

                    $getLastInsertId = Parapheur::all()->max('id');
                    $parapheur = Parapheur::create([
                        'name' => 'parapheur'. $getLastInsertId + 1,
                        'project_id' => $modelItem->project_id,
                        'user_id' => $request->user_assign
                    ]);
                    $parapheurId = $parapheur->id;
                }else{
                    $parapheurId = $getParapheurUserAssign->id;
                }

                //Update Media table with new datas for receiver user
                $mediaData->model_type = Parapheur::class;
                $mediaData->model_id = 0;
                $mediaData->uuid = Str::uuid();
                $mediaData->version = $mediaData->version + 1;
                $mediaData->parapheur_id = $parapheurId;
                $mediaData->save();
            } else{

                //Get Folder
                $user = User::with('multiFolders')->where('id', $request->user_assign)->first();
                $folder = $user->multiFolders->first();
                //Update Media table with new datas for receiver user
                $mediaData->model_type = Folder::class;
                $mediaData->model_id = $folder->id;
                $mediaData->uuid = Str::uuid();
                $mediaData->version = $mediaData->version + 1;
                $mediaData->save();

            }

        }
        else{
            if($request->visibility=="private"){
                $parapheur = Parapheur::where('user_id', $request->user_assign)->first();
                $parapheurId = $parapheur->id;
                //Update Media table with new datas for receiver user
                $mediaData->model_type = Parapheur::class;
                $mediaData->model_id = 0;
                $mediaData->uuid = Str::uuid();
                $mediaData->version = $mediaData->version + 1;
                $mediaData->parapheur_id = $parapheurId;
                $mediaData->save();
            } else{
                //Get Folder
                $user = User::with('multiFolders')->where('id', $request->user_assign)->first();
                $folder = $user->multiFolders->first();
                //Update Media table with new datas for receiver user
                $mediaData->model_type = Folder::class;
                $mediaData->model_id = $folder->id;
                $mediaData->uuid = Str::uuid();
                $mediaData->version = $mediaData->version + 1;
                $mediaData->save();
            }



        }

        //store data in operation table
            Operation::create([
            'deadline' => $request->deadline,
            'priority' => $request->priority,
            'status' => $request->visibility,
            'user_id_sender' => \Auth::user()->id,
            'user_id_receiver' => $request->user_assign,
            'media_id' => $request->media_id,
            'message' => $request->message,
            'receive_mail_notification' => $request->boolean('flexCheckChecked'),
            'operation_type' => $request->send_validation_workflow,
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


        /*$mediaData->setCustomProperty('model_type', Parapheur::class);
      $mediaData->setCustomProperty('model_id', 0);
      $mediaData->setCustomProperty('collection_name', 'files');
      $mediaData->setCustomProperty('conversions_disk', 'public');
      $mediaData->setCustomProperty('disk', 'public');
      $mediaData->setCustomProperty('version', $mediaData->version + 1);
      $mediaData->setCustomProperty('parapheur_id', $parapheur);
      $mediaData->save();*/

        /*return redirect()
            ->route('operation.index', [$newWorkflowValidate])
            ->withStatus('Votre document a été envoyé avec succès.');*/

        return redirect()->back()->with('message', 'Votre document a été envoyé avec succès.');
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
