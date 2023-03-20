<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\Parapheur;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class createDocumentController extends Controller
{


    public function create(Request $request){

        $folderId= $request->folder_id;
        $parapheurId= $request->parapheur_id;

        $children_level_n = Folder::with('project')
            ->whereHas('project.users', function($query) {
                $query->where('id', auth()->id());
            })
            ->whereNull('parent_id')
            ->with('subChildren')
            ->get();

        return view('front.document.create', compact('children_level_n', 'folderId', 'parapheurId'));
    }

    public function upload (Request $request){

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($request->description1);

        $content = $pdf->download()->getOriginalContent();
        Storage::put('public/pdf_generator.pdf',$content);

        /*if($request->folder_id != 0){
            $folder = Folder::findOrFail($request->folder_id);
            $folder->addMedia(storage_path('tmp/uploads/' . $content))->toMediaCollection('files');
            Media::where('model_id', $folder->id)->update([
                'created_by' => \Auth::user()->id
            ]);
        } elseif ($request->parapheur_id != 0){
            $parapheur = Parapheur::findOrFail($request->parapheur_id);
            //$dev = Storage::put('public/pdf_generator.pdf',$content);
            //dd(storage_path('public/pdf_generator.pdf'));
            $media = $parapheur->addMedia('public/pdf_generator.pdf')->toMediaCollection('files');
            $media->created_by = \Auth::user()->id;
            $media->parapheur_id = $parapheur->id;
            $media->model_id = 0;
            $media->save();
        }*/

        return $pdf->stream();

        /*$pdf = PDF::loadView('resume', $request->description1);
        return $pdf->stream('resume.pdf');*/

       /* return response()->json([
            'name'          => $request->description1,
            'original_name' => "dfsd",
        ]);*/
    }
}
