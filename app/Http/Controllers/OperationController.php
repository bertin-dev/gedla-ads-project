<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOperationRequest;
use App\Models\Folder;
use App\Models\Operation;
use App\Models\Parapheur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
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

        //Vérifier si la table parapheur contient déjà l'id rechercher si oui alors récupérer l'id et l'incrémenter de
        //plus 1 si non alors créer un parapheur et recupérer son id.


        //get Media
        $mediaData = Media::find($request->media_id);

        //Get Folder
        $folder = Folder::find($mediaData->model_id);


        //Get User
        $user = User::find($request->user_assign);
        if($user->parapheur_id == null){
            $parapheur = Parapheur::create([
                'name' => 'dev',
                'description' => 'developer',
                'project_id' => $folder->project_id
            ]);
            $user->update(['parapheur_id' => $parapheur->id]);
            $parapheur = $parapheur->id;
        }else{
            $parapheur = $user->parapheur_id;
        }

        //$mediaData->clearMediaCollection('files');

        /*$mediaData->where('conversions_disk', null)->update([
            'model_type' => Parapheur::class,
            'model_id' => $parapheur,
            'collection_name' => 'files',
            'conversions_disk' => 'public',
            'disk' => 'public',
            'version' => $mediaData->version + 1,
            'parapheur_id' => $parapheur
        ]);*/



        $mediaData->cursor()->each(fn (Media $media) => $mediaData->update([
            'model_type' => Parapheur::class,
            'model_id' => $parapheur,
            'uuid' => Str::uuid(),
            'conversions_disk' => $media->disk,
            'parapheur_id' => $parapheur
        ]));


        /*$mediaData->setCustomProperty('model_type', Parapheur::class);
        $mediaData->setCustomProperty('model_id', 0);
        $mediaData->setCustomProperty('collection_name', 'files');
        $mediaData->setCustomProperty('conversions_disk', 'public');
        $mediaData->setCustomProperty('disk', 'public');
        $mediaData->setCustomProperty('version', $mediaData->version + 1);
        $mediaData->setCustomProperty('parapheur_id', $parapheur);

        $mediaData->save();*/

        //dd($mediaData->toArray());

        $newWorkflowValidate = Operation::create([
            'deadline' => $request->deadline,
            'priority' => $request->priority,
            'status' => $request->visibility,
            'user_id_sender' => \Auth::user()->id,
            'user_id_receiver' => $request->user_assign,
            'media_id' => $request->media_id,
            'message' => $request->message,
            'receive_mail_notification' => $request->boolean('flexCheckChecked'),
            'operation_type' => $request->init_validation_workflow,
            'operation_state' => 'pending',
            'num_operation' => (string) Str::orderedUuid(),
        ]);



        return redirect()
            ->route('operation.index', [$newWorkflowValidate])
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
