<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\Project;
use Illuminate\Http\Request;
use PHPUnit\Exception;
use thiagoalessio\TesseractOCR\TesseractOCR;

class OcrController extends Controller
{
    //OCR
    public function openOCR()
    {
        $children_level_n = Folder::with('project')
            ->whereHas('project.users', function($query) {
                $query->where('id', auth()->id());
            })
            ->whereNull('parent_id')
            ->with('subChildren')
            ->get();

        $parents = Folder::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('front.ocr.index', compact('children_level_n', 'parents'));
    }

    public function storeImgOCR(Request $request)
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

        $path = public_path('uploads');

        try {
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
        } catch (\Exception $e) {
        }

        $file = $request->file('file');

        $name = uniqid() . '_' . trim($file->getClientOriginalName());

        $file->move($path, $name);

        /*try {
            $fileRead = (new TesseractOCR('tmp/uploads/'. $name))
                ->lang('fra')
                ->run();
            dd($fileRead);
        } catch (Exception $e){
            dd($e->getMessage());
        }*/

        return response()->json([
            'name'          => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }


    public function postUploadOCR(Request $request)
    {
        //dd($request->folder_id);
        foreach ($request->input('files', []) as $file) {
            try {
                $fileRead = (new TesseractOCR('uploads/' . $file))
                    ->lang('fra')
                    ->run();
                //dd($fileRead);


                /*$phpWord = new \PhpOffice\PhpWord\PhpWord();
                $section = $phpWord->addSection();
                $text = $section->addText($fileRead);
                //$text = $section->addText($request->get('emp_salary'));
                //$text = $section->addText($request->get('emp_age'),array('name'=>'Arial','size' => 20,'bold' => true));

                $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
                $objWriter->save('Appdividend.docx');
                response()->download(public_path('phpflow.docx'));*/


            } catch (Exception $e){
                dd($e->getMessage());
            }
            //$folder->addMedia(public_path('uploads/' . $file))->toMediaCollection('files');
        }

        //chargement de la barre lattérale gauche
        $children_level_n = Folder::with('project')
            ->whereHas('project.users', function($query) {
                $query->where('id', auth()->id());
            })
            ->whereNull('parent_id')
            ->with('subChildren')
            ->get();


        return view('front.ocr.show', compact('children_level_n', 'fileRead'))->with('status', 'OCR réussi');
    }


    public function postUploadFile(){

    }
}
