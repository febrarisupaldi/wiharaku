<?php

namespace App\Http\Controllers\Api\Vihara;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PublicViharaController extends Controller
{
    public function show(string $slug): View
    {
        return view('pages.vihara.show', [
            'slug' => $slug,
        ]);
    }
}
