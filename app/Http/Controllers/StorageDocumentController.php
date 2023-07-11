<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Auditable;
use App\Models\AuditLog;
use App\Models\Folder;
use App\Models\ValidationStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class StorageDocumentController extends Controller
{
    use Auditable;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        abort_if(Gate::denies('storage_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $getMedias = Media::where('archived', 0)
            ->where('saved', 1)
            ->where('state', 'unlocked')
            ->get()
            ->sortByDesc('created_at');

        $getValidationDatas = ValidationStep::where('user_id', auth()->id());

        $children_level_n = Folder::with('project')
            ->whereHas('project.users', function ($query) {
                $query->where('id', auth()->id());
            })
            ->whereNull('parent_id')
            ->with('subChildren')
            ->get();

        return view('front.storage.index', compact('getMedias', 'getValidationDatas', 'children_level_n'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        abort_if(Gate::denies('store_document'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $getMedia = Media::findOrFail($request->id);
        $getMedia->saved = 1;
        $getMedia->saved_at = date('Y-m-d H:i:s', time());

        //SAVE OPERATION IN LOG
        self::trackOperations($getMedia->id,
            "STORE_DOCUMENT",
            $this->templateForDocumentHistoric(ucfirst(auth()->user()->name) . ' a sauvegardé le document ' . strtoupper(substr($getMedia->file_name, 14))),
            'success',
            auth()->id(),
            null,
            auth()->user()->name,
            ucfirst(auth()->user()->name) . ' a sauvegardé le document ' . strtoupper(substr($getMedia->file_name, 14)),
        );

        $getMedia->save();
        return response()->json([
            'status' => "Votre document a été sauvegardé avec succès.",
        ], Response::HTTP_OK);
    }

    private function templateForDocumentHistoric($params = '')
    {
        return '<div class="row schedule-item>
                <div class="col-md-2">
                <time class="timeago">Le ' . date('d-m-Y à H:i:s', time()) . '</time>
                </div>
                <div class="col-md-12">
                <p>' . $params . '</p>
                </div>
                </div>';
    }

    /**
     * restore a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        abort_if(Gate::denies('restore_document'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $getMedia = Media::findOrFail($id);
        $getMedia->saved = 0;
        $getMedia->saved_at = null;
        //SAVE OPERATION IN LOG
        self::trackOperations($getMedia->id,
            "RESTORE_DOCUMENT",
            $this->templateForDocumentHistoric(ucfirst(auth()->user()->name) . ' a restauré le document ' . strtoupper(substr($getMedia->file_name, 14))),
            'success',
            auth()->id(),
            null,
            auth()->user()->name,
            ucfirst(auth()->user()->name) . ' a restauré le document ' . strtoupper(substr($getMedia->file_name, 14)),
        );

        $getMedia->save();
        return redirect()->back()->with('status', 'Votre document a été restauré avec succès.');
    }
}
