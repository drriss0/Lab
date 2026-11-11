<?php

namespace App\Controllers;

class Kelola extends BaseController
{
    public function index()
    {
        if ($redirect = $this->checkAuth()) return $redirect;
        if ($redirect = deny_if_not('admin')) return $redirect;

        return view('kelola/index', [
            'active_menu' => 'kelola',
            'title'       => 'Kelola Sistem'
        ]);
    }
}
