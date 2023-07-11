<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\Auditable;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class ArchiveController extends Controller
{
    use Auditable;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        abort_if(Gate::denies('archive_file_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $gedFiles = Storage::disk('archives')->allFiles();
        //$fileContents = Storage::disk('ged')->get($fileName);

        /*$medias = Media::where('collection_name', 'files')
            ->where('archived', true)
            ->get();*/

        return view('admin.archive.index', compact('gedFiles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        abort_if(Gate::denies('archive_add_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $medias = Media::where('archived', false)
            ->whereNull('archived_at')
            ->get();
        return view('admin.archive.create', compact('medias'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        abort_if(Gate::denies('archive_store_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $media = Media::findOrFail($request->media_archived_id);
        $media_name = strtoupper(substr($media->file_name, 14));

        // Mettez à jour la colonne 'archived_at' dans la base de données
        $media->update([
            'disk' => 'archives',
            'conversions_disk' => 'archives',
            'archived' => true,
            'archived_at' => now(),
            'password' => bcrypt($request->inputPassword)
        ]);

        self::trackOperations($media->id,
            "ARCHIVE_DOCUMENT",
            $this->templateForDocumentHistoric(ucfirst(auth()->user()->name) . ' vient d\'archiver le document ' . $media_name),
            'success',
            null,
            auth()->id(),
            '',
            ucfirst(auth()->user()->name) . ' vient d\'archiver le document ' . $media_name);

        //Déplacez le document vers le dossier d'archivage
        $publicPath = 'public/' . $media->id;
        $ArchivesPath = 'public/archives/' . $media->id;
        Storage::move($publicPath, $ArchivesPath);

        // Vérifiez que le dossier source existe
        if (File::isDirectory($publicPath)) {
            // Déplacez le dossier vers la destination
            //File::move($sourceDir, $destinationDir);
            //supprimer le dossier
            File::deleteDirectories($publicPath);
            //echo "Le dossier a été déplacé avec succès.";
        }

        /*if($request->user=="admin"){
            return Redirect::back()->with('success', 'Le document '.$media_name.' a été archivé avec succès.');
        }
        return \response()->json([
            'result' => 'Le document '.$media_name.' a été archivé avec succès.'
        ], Response::HTTP_ACCEPTED);*/
        return redirect()->back()->with('status', 'Le document ' . $media_name . ' a été archivé avec succès.');
    }

    public function restore(Request $request){
        $media = Media::findOrFail(substr($request->file_name, 0, 1));
        $media_name = strtoupper(substr($media->file_name, 14));

        /*$gedFilePath = storage_path("app/public/archives");
        $fileContents = Storage::disk('archives')->get($request->file_name);
        Storage::disk('local')->put($fileName, $fileContents);
        Storage::disk('archive')->delete($gedFilePath);*/

        //Mettez à jour la colonne 'archived_at' dans la base de données
        $media->update([
            'disk' => 'public',
            'conversions_disk' => 'public',
            'archived' => false,
            'archived_at' => null
        ]);

        self::trackOperations($media->id,
            "RESTORE_ARCHIVE_DOCUMENT",
            $this->templateForDocumentHistoric(ucfirst(auth()->user()->name) . ' vient de restorer le document ' . $media_name . ' qui avait été archivé'),
            'success',
            null,
            auth()->id(),
            '',
            ucfirst(auth()->user()->name) . ' vient de restorer le document ' . $media_name . ' qui avait été archivé');

        //Déplacez le document vers le dossier d'archivage
        $ArchivesPath = 'public/archives/' . $request->file_name;
        $publicPath = 'public/' . $media->id;
        Storage::move($ArchivesPath, $publicPath);

        // Vérifiez que le dossier source existe
        if (File::isDirectory('public/archives/' . $media->id)) {
            File::deleteDirectories('public/archives/' . $media->id);
        }


        return Redirect::back()->with('success', 'Le document '.$media_name.' a été dearchivé avec succès.');

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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($fileName)
    {
        abort_if(Gate::denies('archive_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $media = Storage::disk('archives')->get($fileName);

        return new Response($media, 200, [
            'Content-Type' => Storage::disk('archives')->mimeType($fileName),
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
        ]);

        //return view('admin.archive.show', compact('media'));
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
      abort_if(Gate::denies('archive_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        //Storage::disk('ged')->delete($gedFilePath);
    }
}
