<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\User;
use App\Models\ValidationStep;
use Elasticsearch\ClientBuilder;
use Elasticsearch\Common\Exceptions\NoNodesAvailableException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SearchController extends Controller
{
    public function search(Request $request, Folder $folder)
    {

            $searchTerm = $request->input('q');
            $documentType = $request->input('type');

            //$foldersUsers = User::with('multiFolders')->findOrFail(auth()->id());
            //$getMedias = $foldersUsers->multiFolders->where('id', $folder->id)->first();

            //dd($getMedias->first()->files->toArray());
            /*foreach ($getMedias as $getMedia){
                dd($getMedia->files->toArray());
            }*/


            /*$results = User::where('id', Auth::id())->search($searchTerm)->whereHas('documents', function ($query) {
                $query->where('user_id', Auth::id());
            })->paginate(10);*/

            if ($request->filled('q') || $request->filled('type')){

                $folders = Folder::search($searchTerm)->get();
                dd($folders->load('media'));


                $medias = $folder->whereHas('media', function($q) use($searchTerm) {
                    $q->whereRaw("MATCH(name, file_name, mime_type) AGAINST(? IN BOOLEAN MODE)", array($searchTerm));
                })
                    ->get();

                dd($medias->toArray());

                $medias = $folder->media
                    ->where(function ($query) use ($searchTerm, $documentType) {
                        $query->where('name', 'like', '%'.$searchTerm.'%')
                            ->orWhere('mime_type', 'like', '%'.$documentType.'%');
                    });
            }else {
                $medias = $folder->media;
            }
            dd($medias->toArray());

            //$collection = collect($getMedias->files);
            //dd($collection->toArray());


            $children_level_n = $folder->with('project')
                ->whereHas('project.users', function($query) {
                    $query->where('id', auth()->id());
                })
                ->whereNull('parent_id')
                ->with('subChildren')
                ->get();

        $users = User::where('id', '!=', \Auth::user()->id)
            ->whereHas('multiFolders')
            ->pluck('name', 'id')
            ->prepend(trans('global.pleaseSelect'), '');

        $getValidationData = ValidationStep::where('user_id', auth()->id());

        return view('front.folders.show_files', compact('folder','children_level_n', 'users', 'getValidationData'));
    }
}
