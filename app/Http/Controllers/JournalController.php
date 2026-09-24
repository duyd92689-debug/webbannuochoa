<?php

namespace App\Http\Controllers;

use App\Models\Article;

class JournalController extends Controller
{
    public function index()
    {
        return view('store.journal', ['articles' => Article::where('is_published', true)->latest()->paginate(9)]);
    }

    public function show(Article $article)
    {
        abort_unless($article->is_published, 404);
        return view('store.article', compact('article'));
    }
}
