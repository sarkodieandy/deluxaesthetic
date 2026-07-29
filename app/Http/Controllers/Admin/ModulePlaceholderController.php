<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ModulePlaceholderController extends Controller
{
    public function __invoke(string $title): View
    {
        return view('admin.modules.placeholder', [
            'title' => $title,
            'moduleKey' => $title,
        ]);
    }
}
