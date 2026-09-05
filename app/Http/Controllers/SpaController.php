<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class SpaController extends Controller
{
    public function index(): Response
    {
        $indexPath = public_path('build/index.html');

        if (!File::exists($indexPath)) {
            abort(503, 'Frontend not built. Run: cd frontend && npm install && npm run build');
        }

        return response(File::get($indexPath), 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }
}
