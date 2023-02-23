<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyFolderRequest;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Image\Image;

class SignatureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        abort_if(Gate::denies('signature_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $allMediaWithSignature = Media::with('signedBy', 'createdBy')->whereNotNull('signed_by')->get();
        return view('admin.signature.index', compact('allMediaWithSignature'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        abort_if(Gate::denies('signature_access_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $users = User::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        return view('admin.signature.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $folderPath = storage_path('tmp/uploads/');
        $image_parts = explode(";base64,", $request->signed);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $file = $folderPath . uniqid() . '.'.$image_type;

        file_put_contents($file, $image_base64);

        $userSigned = User::find($request->user_signature);
        try {
            $media = $userSigned->addMedia($file)->toMediaCollection('signature');
        } catch (FileDoesNotExist | FileIsTooBig $e) {
        }
        $media->created_by = \Auth::user()->id;
        $media->collection_name = 'signature';
        $media->signed_by = $request->user_signature;
        $media->model_id = 0;
        $media->save();


        return back()->with('success', 'success Full upload signature');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        abort_if(Gate::denies('signature_access_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $media = Media::findOrFail($id);
        return view('admin.signature.show', compact('media'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminat0e\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        abort_if(Gate::denies('signature_access_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $media = Media::find($id);
        $users = User::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');;
        return view('admin.signature.edit', compact('media', 'users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        //LA MODIFICATION DE LA SIGNATURE N'EST PAS ENCORE EFFECTIF
        /*$folderPath = storage_path('upload/');
        $image_parts = explode(";base64,", $request->signed);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $file = $folderPath . uniqid() . '.'.$image_type;
        file_put_contents($file, $image_base64);


        //$mediaEdit = Media::find($id);
        //dd($mediaEdit->toArray());

        $userSigned = User::find($request->user_signature);
        try {
            $media = $userSigned->addMedia($file)->toMediaCollection('signature');
            $media->created_by = \Auth::user()->id;
            $media->signed_by = $request->user_signature;
            $media->model_id = 0;
            $media->save();

        } catch (FileDoesNotExist | FileIsTooBig $e) {
        }

        return back()->with('success', 'success Full update signature');*/
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        abort_if(Gate::denies('signature_access_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        //$folder->delete();

        return back();
    }

    public function massDestroy(MassDestroyFolderRequest $request)
    {
        Media::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }


    public function upload()
    {
        $users = User::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $children_level_n = Folder::with('project')
            ->whereHas('project.users', function($query) {
                $query->where('id', auth()->id());
            })
            ->whereNull('parent_id')
            ->with('subChildren')
            ->get();

        return view('admin.signature.upload', compact('children_level_n', 'users'));
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

    public function postUpload(Request $request)
    {
        $userSigned = User::find($request->user_signature);


        foreach ($request->input('files', []) as $file) {

            try {
                $media = $userSigned->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('signature');
            } catch (FileDoesNotExist | FileIsTooBig $e) {
            }

            Image::load(storage_path('tmp/uploads/' . $file))->width(1024)->height(1024);

            $media->created_by = Auth()->id();
            $media->collection_name = $request->initial;
            $media->signed_by = $request->user_signature;
            $media->model_id = 0;
            $media->save();

        }

        return back()->with('success', 'success Full upload signature');
        //return redirect()->route('folders.show', $folder)->withStatus('Files has been uploaded');
    }
}
