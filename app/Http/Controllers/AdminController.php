<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Método para renderizar la vista del administrador
    public function index() {
        return view('admin.index');
    }
}
