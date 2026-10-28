<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('pages/home', ['title' => 'ShipFromStore']);
    }

    public function dashboard(): string
    {
        return view('pages/dashboard', ['title' => 'Painel | ShipFromStore']);
    }
}
