<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\Operation;
use App\Models\Parapheur;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ParapheurController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $children_level_n = Folder::with('project')
            ->whereHas('project.users', function($query) {
                $query->where('id', auth()->id());
            })
            ->whereNull('parent_id')
            ->with('subChildren')
            ->get();

        $parapheur = Parapheur::with('medias')->where('user_id', auth()->id())->get();

        return view('front.parapheur.index', compact('children_level_n', 'parapheur'));

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
     * @return \Illuminate\Http\Response
     */
    public function show(Parapheur $parapheur)
    {
        //
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


    public function download(Request $request){

        $media = Media::find($request->id);
        return response()->download($media->getPath(), $media->file_name);
    }
}
