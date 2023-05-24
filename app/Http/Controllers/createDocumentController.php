<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Auditable;
use App\Models\AuditLog;
use App\Models\Folder;
use App\Models\Parapheur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use NcJoes\OfficeConverter\OfficeConverter;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;
use Smalot\PdfParser\Parser;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class createDocumentController extends Controller
{
    use Auditable;

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

        $fileNameWithExension = '';
        $media = new Media();
        $path = storage_path('tmp/uploads');
        try {
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
        } catch (\Exception $e) {
        }


        switch ($request->documentFormat){
            case "pdf":
                $pdf = App::make('dompdf.wrapper');
                $pdf->loadHTML($request->description1);
                // (Optional) Setup the paper size and orientation
                //$pdf->setPaper('A4', 'landscape');
                // Render the HTML as PDF
                //$pdf->render();
                //$pdf->setBasePath(storage_path('tmp/uploads'));
                //$pdf->stream("codexworld", array("Attachment" => 1));

                //*$output = $pdf->output();
                //file_put_contents(storage_path('tmp/uploads/pdf/my_stored_file.pdf'), $output);

                //dd($pdf);

                //$content = $pdf->download()->getOriginalContent();
                //Storage::put('tmp/uploads/pdf_generator.pdf', $content);

                $fileNameWithExension =  uniqid() . '_' .trim($request->fileName) . '.pdf' ;
                $pdf->save($path . '/' . $fileNameWithExension);

                //dd(Storage::get('storage/tmp/uploads/pdf_generator.pdf'));
                //$dev = Storage::url('pdf/pdf_generator.pdf');

                //return $pdf->stream();

                /*$pdf = PDF::loadView('resume', $request->description1);
                return $pdf->stream('resume.pdf');*/
            break;
            case "word":
                $fileNameWithExension =  uniqid() . '_' .trim($request->fileName) . '.docx';
                $this->convertDocumentFormat($request->description1, $path, "Word2007", $fileNameWithExension);
                break;

            case "odt":
                $fileNameWithExension =  uniqid() . '_' .trim($request->fileName) . '.odt';
                $this->convertDocumentFormat($request->description1, $path, "ODText", $fileNameWithExension);
                break;

            case "rtf":
                $fileNameWithExension =  uniqid() . '_' .trim($request->fileName) . '.rtf';
                $this->convertDocumentFormat($request->description1, $path, "RTF", $fileNameWithExension);
                break;

            case "xlsx":
                break;

            case "csv":
                break;

            case "html":
                $fileNameWithExension =  uniqid() . '_' .trim($request->fileName) . '.html';
                $this->convertDocumentFormat($request->description1, $path, "HTML", $fileNameWithExension);
                break;

            default:
        }

        if($request->folder_id != 0){
            $folder = Folder::findOrFail($request->folder_id);
            $media = $folder->addMedia(storage_path('tmp/uploads/' . $fileNameWithExension))->toMediaCollection('files');
            Media::where('model_id', $folder->id)->update([
                'created_by' => \Auth::user()->id
            ]);
        } elseif ($request->parapheur_id != 0){
            $parapheur = Parapheur::findOrFail($request->parapheur_id);
            $media = $parapheur->addMedia(storage_path('tmp/uploads/' . $fileNameWithExension))->toMediaCollection('files');
            $media->created_by = \Auth::user()->id;
            $media->parapheur_id = $parapheur->id;
            $media->model_id = 0;
            $media->save();
        }

        $getLog = AuditLog::where('media_id', $media->id)
            ->where('operation_type', 'CREATE_DOCUMENT')
            ->where('current_user_id', auth()->id())
            ->get();
        if(count($getLog) === 0){
            self::trackOperations($media->id,
                "CREATE_DOCUMENT",
                $this->templateForDocumentHistoric(ucfirst(auth()->user()->name) .' a crée le document '. substr($media->file_name, 14)),
                'success',
                auth()->id(),
                null,
                auth()->user()->name,
                ucfirst(auth()->user()->name) .' a crée le document '. substr($media->file_name, 14)
            );
        }
        return response()->json([
            'name'          => $fileNameWithExension,
            'original_name' => $request->fileName,
        ]);

    }

    private function convertDocumentFormat($htmlText, $path1, $format, $fileNameWithExension1){
        //load phpword
        $pw = new PhpWord();
        //add html content
        $section = $pw->addSection();
        Html::addHtml($section, $htmlText, false, false);

        //save to docx on server
        $pw->save($path1 . '/' . $fileNameWithExension1, $format);
    }

    public function edit(Request $request){
        $folderId= $request->folder_id;
        $parapheurId= $request->parapheur_id;

        $getMedia = Media::findOrFail($request->mediaId);

        // Parse PDF file and build necessary objects.
        $parser = new Parser();
        $pdf = $parser->parseFile($getMedia->getPath());

        $text = $pdf->getText();
        $text = nl2br($text);


        //$converter = new OfficeConverter($getMedia->getPath());
        //$converter->convertTo('output-file.pdf'); //generates pdf file in same directory as test-file.docx
        //$converter->convertTo('output-file.html'); //generates html file in same directory as test-file.docx

        //to specify output directory, specify it as the second argument to the constructor
        //$converter = new OfficeConverter($getMedia->getPath(), storage_path('tmp/uploads'));
        //$converter = new OfficeConverter($getMedia->getPath(), storage_path('tmp/uploads'), '/Applications/LibreOffice.app/Contents/MacOS/soffice', true );
        //dd($converter->convertTo(storage_path('pipooo.html')));



        $children_level_n = Folder::with('project')
            ->whereHas('project.users', function($query) {
                $query->where('id', auth()->id());
            })
            ->whereNull('parent_id')
            ->with('subChildren')
            ->get();

        return view('front.document.edit', compact('children_level_n', 'folderId', 'parapheurId', 'getMedia', 'text'));
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
