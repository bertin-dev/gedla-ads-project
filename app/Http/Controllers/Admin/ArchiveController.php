<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class ArchiveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        abort_if(Gate::denies('archive_file_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $gedFiles = Storage::disk('ged')->allFiles();
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
        $gedFiles = Storage::disk('public')->allFiles();
        return view('admin.archive.create', compact('gedFiles'));
    }

    public function archive($id)
    {
        $document = Media::findOrFail($id);
        $document->update(['archived_at' => now()]);
        return redirect()->back()->with('success', 'Le document a été archivé avec succès.');
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
     * @param  int  $id
     * @return Response
     */
    public function show($fileName)
    {
        abort_if(Gate::denies('archive_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');


        $media = Storage::disk('ged')->get($fileName);

        return new Response($media, 200, [
            'Content-Type' => Storage::disk('ged')->mimeType($fileName),
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
        //Storage::disk('ged')->delete($gedFilePath);
    }
}
