<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class CalendarController extends Controller
{

    public function index(){

        abort_if(Gate::denies('calendar_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $children_level_n = Folder::with('project')
            ->whereHas('project.users', function($query) {
                $query->where('id', auth()->id());
            })
            ->whereNull('parent_id')
            ->with('subChildren')
            ->get();

        return view('livewire.index', compact('children_level_n'));
    }
}
