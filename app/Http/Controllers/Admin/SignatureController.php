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
        $media = Media::find($id);
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
}
