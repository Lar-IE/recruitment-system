<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PagesController extends Controller
{
    public function jobPosts(): View
    {
        return view('admin.job-posts.index');
    }
}
