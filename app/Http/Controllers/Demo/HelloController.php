<?php

namespace App\Http\Controllers\Demo;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HelloController extends Controller
{
    //
    public function index(){
        return 'hello';
    }
}
