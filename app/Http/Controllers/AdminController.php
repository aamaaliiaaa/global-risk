<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Port;
use App\Models\Country;
use App\Models\PositiveWord;
use App\Models\NegativeWord;
use App\Models\Article;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::all();
        $ports = Port::with('country')->get();
        $countries = Country::all();
        $posWords = PositiveWord::all();
        $negWords = NegativeWord::all();
        $articles = Article::orderBy('published_at', 'desc')->get();

        return view('admin.index', compact('users', 'ports', 'countries', 'posWords', 'negWords', 'articles'));
    }

    public function storePort(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|string',
            'city' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'status' => 'required|string',
            'risk_score' => 'required|integer|min:0|max:100',
        ]);

        Port::create($request->all());

        return redirect()->route('admin.index')->with('success', 'Port created successfully.');
    }

    public function destroyPort(Port $port)
    {
        $port->delete();
        return redirect()->route('admin.index')->with('success', 'Port deleted successfully.');
    }

    public function storeWord(Request $request)
    {
        $request->validate([
            'word' => 'required|string|unique:positive_words,word|unique:negative_words,word',
            'type' => 'required|in:positive,negative',
        ]);

        $word = strtolower($request->word);

        if ($request->type === 'positive') {
            PositiveWord::create(['word' => $word]);
        } else {
            NegativeWord::create(['word' => $word]);
        }

        return redirect()->route('admin.index')->with('success', 'Word added to dictionary.');
    }

    public function destroyWord($type, $id)
    {
        if ($type === 'positive') {
            PositiveWord::destroy($id);
        } else {
            NegativeWord::destroy($id);
        }

        return redirect()->route('admin.index')->with('success', 'Word deleted from dictionary.');
    }

    public function storeArticle(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'author' => 'required|string',
        ]);

        Article::create([
            'title' => $request->title,
            'content' => $request->content,
            'author' => $request->author,
            'published_at' => now(),
        ]);

        return redirect()->route('admin.index')->with('success', 'Article published successfully.');
    }

    public function destroyArticle(Article $article)
    {
        $article->delete();
        return redirect()->route('admin.index')->with('success', 'Article deleted successfully.');
    }
}
