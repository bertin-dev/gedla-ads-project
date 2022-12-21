<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\Request;
use PHPUnit\Exception;
use thiagoalessio\TesseractOCR\TesseractOCR;

class PostUploadOCRController extends Controller
{
    public function create()
    {
        return view('front.folders.create');
    }


    public function upload()
    {
        return view('front.folders.upload');
    }


    public function postUploadOCR(Request $request)
    {

        //dd($_FILES['file']['type']);
        //dd($request->file());
        if(isset($_POST['submit'])){
            # file type
            $type = $_FILES['file']['type'];

            # file name
            $file_name = $_FILES['file']['name'];

            # file size in KB
            $file_size = $_FILES['file']['size'];

            # temp file to upload
            $tmp_file = $_FILES['file']['tmp_name'];

            # file exists
            if(file_exists('uploads/'. $file_name)){
                echo "file exists";
            }

            if(!session_id()){
                session_start();
                $unq = session_id();
            }

            # rename file uploaded and replace special characters with underscores
            $file_name = $unq . '_' . time() . '_' . str_replace(array('!', "@", '#', '$', '%', '^', '&', ' ', '*', '(', ')', ':', ';', ',', '?', '/'. '\\', '~', '`', '-'), '_', strtolower($file_name));

            if (!file_exists('uploads')) {
                mkdir('uploads', 0777, true);
            }

            if(move_uploaded_file($tmp_file, 'uploads/'. $file_name)){
                echo "<p class='alert alert-success'>File uploaded successfully</p>";
            } else {
                echo "<p class='alert alert-danger'>File failed to upload.</p>";
            }



            try {

                $fileRead = (new TesseractOCR('uploads/'. $file_name))
                    ->lang('fra')
                    ->run();
                dd($fileRead);
            } catch (Exception $e){
                dd($e->getMessage());
            }




        }

        return true;
        //return redirect()->route('ocr.index', compact($fileRead));
    }
}
