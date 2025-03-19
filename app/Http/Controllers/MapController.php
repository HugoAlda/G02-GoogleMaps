<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MapController extends Controller
{
    public function index()
    {
        return view('mapa.index');
    }

    public function juego()
    {
        return view('mapa.juego');
    }

    public function partida()
    {
        return view('mapa.partida');
    }
}
