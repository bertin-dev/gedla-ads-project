<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function displayView(){
        return view('admin.users.import_users');
    }

    public function exportUsers() {
        return Excel::download(new UsersExport, 'users.xlsx');
    }
    public function importUsers() {
        Excel::import(new UsersImport,request()->file('file'));
        return redirect()->route('admin.users.index')->with('success', 'Votre Importation a réussie');
    }
}
