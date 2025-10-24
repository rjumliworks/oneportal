<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
     public function index(Request $request){
        switch($request->option){
            case 'lists':
                return [];
            break;
            default:
               return inertia('Modules/HumanResource/Dashboard/Index');
        }   
    }
}
