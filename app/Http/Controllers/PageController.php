<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        $page = Page::where('type', Page::TYPE_MAIN_PAGE)->first();
        return view('index', compact('page'));
    }
}
