<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Auditable;
use App\Models\AuditLog;
use App\Models\Folder;
use App\Models\Operation;
use App\Models\Parapheur;
use App\Models\User;
use App\Models\ValidationStep;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ParapheurController extends Controller
{
    use Auditable;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    /*public function index()
    {
        $children_level_n = Folder::with('project')
            ->whereHas('project.users', function($query) {
                $query->where('id', auth()->id());
            })
            ->whereNull('parent_id')
            ->with('subChildren')
            ->get();

        $getParapheur = Parapheur::where('user_id', auth()->id())->first();
        if($getParapheur == null){
            $getLastInsertId = Parapheur::all()->max('id');
            Parapheur::create([
                'name' => 'parapheur'. $getLastInsertId + 1,
                'project_id' => 1,
                'user_id' => auth()->id()
            ]);
        }

        $parapheur = $getParapheur->with('medias')->where('user_id', auth()->id())->first();

        return view('front.parapheur.index', compact('children_level_n', 'parapheur'));

    }*/


    public function upload()
    {
        $children_level_n = Folder::with('project')
            ->whereHas('project.users', function($query) {
                $query->where('id', auth()->id());
            })
            ->whereNull('parent_id')
            ->with('subChildren')
            ->get();

        return view('front.parapheur.upload', compact('children_level_n'));
    }


    public function postUpload(Request $request)
    {
        $parapheur = Parapheur::findOrFail($request->parapheur_id);

        foreach ($request->input('files', []) as $file) {
            $media = $parapheur->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('files');
            $media->created_by = \Auth::user()->id;
            $media->parapheur_id = $parapheur->id;
            $media->model_id = 0;
            $media->save();

            $getLog = AuditLog::where('media_id', $media->id)
                ->where('current_user_id', auth()->id())
                ->where('operation_type', 'IMPORT_DOCUMENT')
                ->get();
            if(count($getLog) === 0){
                self::trackOperations($media->id,
                    "IMPORT_DOCUMENT",
                    $this->templateForDocumentHistoric(ucfirst(auth()->user()->name) .' vient d\'importer le document '. strtoupper(substr($media->file_name, 14))),
                    'success',
                    null,
                    auth()->id(),
                    '',
                    ucfirst(auth()->user()->name) .' vient d\'importer le document '. strtoupper(substr($media->file_name, 14)));
            }
        }

        return redirect()->route('parapheur.show', $parapheur)->withStatus('Files has been uploaded');
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Parapheur  $parapheur
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show(Parapheur $parapheur)
    {
        /*$getMediaDocument = Media::with('operations', 'usersListSelectedForWorkflowValidations')->find(29);
        //dd($getMediaDocument->toArray());
        //dd(json_decode($getMediaDocument->step_workflow)->step_workflow);
        foreach(json_decode($getMediaDocument->step_workflow)->step_workflow as $item){
            if($item==5){
                dd(json_decode($getMediaDocument->step_workflow));
            }
        }*/

        $children_level_n = Folder::with('project')
            ->whereHas('project.users', function($query) {
                $query->where('id', auth()->id());
            })
            ->whereNull('parent_id')
            ->with('subChildren')
            ->get();
        //dd($parapheur->toArray());

        $parapheurWithMedia = $parapheur->with('medias')
            ->where('user_id', auth()->id())
            ->first();

        $getValidationDatas = ValidationStep::where('user_id', auth()->id());

        return view('front.parapheur.show_files', compact('children_level_n', 'parapheurWithMedia', 'getValidationDatas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Parapheur  $parapheur
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Parapheur $parapheur)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Parapheur  $parapheur
     * @return \Illuminate\Http\Response
     */
    public function destroy(Parapheur $parapheur)
    {
        //
    }

    private function templateForDocumentHistoric($params = ''){
        return '<div class="row schedule-item>
                <div class="col-md-2">
                <time class="timeago">Le '.date('d-m-Y à H:i:s', time()).'</time>
                </div>
                <div class="col-md-12">
                <p>' .$params . '</p>
                </div>
                </div>';
    }
}
