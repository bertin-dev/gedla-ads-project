<?php

namespace App\Http\Controllers;

use App\Events\DocumentAdded;
use App\Events\DocumentUpdated;
use App\Events\validationStepCompleted;
use App\Models\Notification;
use App\Models\User;
use App\Models\ValidationStep;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $user = auth()->user();
        $notifications = $user->notifications();

        return view('layouts.front', compact('notifications'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show(Notification $notification)
    {
        $notification->update(['read_at' => now()]);
        return view('layouts.front', compact('notification'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();

        return redirect()->route('notifications.index');
    }


    public function documentAdded(Media $media)
    {
        /*$users = User::all();
        foreach ($users as $user) {
            $user->notifications()->create([
                'subject' => 'Nouveau document ajouté',
                'body' => 'Le document "' . $media->name . '" a été ajouté à la GED.',
                'media_id' => $media->id,
            ]);
        }*/

        $detailsMedia = [
            'subject' => 'Nouveau document ajouté',
            'body' => 'Le document "' . strtoupper(substr($media->name, 14)) . '" a été ajouté avec succès dans GEDLA-ADS.',
            'media_id' => $media->id,
            'media_name' => $media->file_name,
            'validation_step_id' => 0,
        ];

        event(new DocumentAdded($detailsMedia));
    }

    public function documentUpdated(Media $media)
    {
        /*$users = User::all();

        foreach ($users as $user) {
            $user->notifications()->create([
                'subject' => 'Document modifié',
                'body' => 'Le document "' . $media->name . '" a été modifié.',
                'media_id' => $media->id,
            ]);
        }*/

        $detailsMedia = [
            'subject' => 'Document modifié',
            'body' => 'Le document "' . strtoupper(substr($media->name, 14)) . '" a été modifié.',
            'media_id' => $media->id,
            'media_name' => $media->file_name,
            'validation_step_id' => 0,
        ];
        event(new DocumentUpdated($detailsMedia));
    }

    public function validationStepCompleted(ValidationStep $validationStep)
    {
        /*$users = $validationStep->user()->get();

        foreach ($users as $user) {
            $user->notifications()->create([
                'subject' => 'Étape du workflow terminée',
                'body' => 'L\'étape "' . $validationStep->order . '" du document "' . $validationStep->media->name . '" a été terminée.',
                'media_id' => $validationStep->media->id,
                'validation_step_id' => $validationStep->id,
            ]);
        }*/

        $validationStepDetailsMedia = [
            'subject' => 'Étape du workflow terminée',
            'body' => 'La dernière étape N°("' . $validationStep->order . '") dans le circuit de validation du document "' . strtoupper(substr($validationStep->media->name, 14)) . '" est terminé.',
            'media_id' => $validationStep->media->id,
            'media_name' => $validationStep->media->name,
            'validation_step_id' => $validationStep->id,
        ];
        event(new validationStepCompleted($validationStepDetailsMedia));
    }
}
