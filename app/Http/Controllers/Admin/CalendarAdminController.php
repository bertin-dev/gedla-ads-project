<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class CalendarAdminController extends Controller
{
    public function index(){
        abort_if(Gate::denies('calendar_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('livewire.index-admin');
    }
}
