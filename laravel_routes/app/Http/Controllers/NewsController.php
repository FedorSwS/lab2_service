<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NewsController extends Controller
{
    public function index()
    {
        $news = DB::table('news')->orderByDesc('published_at')->get();
        return response()->json(['page' => 'news.index', 'items' => $news]);
    }

    public function show(int $id)
    {
        $item = DB::table('news')->where('id', $id)->first();
        abort_if(!$item, 404);
        return response()->json(['page' => 'news.show', 'item' => $item]);
    }

    public function toggleLike(Request $request, int $id)
    {
        $token = $request->cookie('news_client_token', $request->ip());
        $exists = DB::table('news_likes')
            ->where('news_id', $id)
            ->where('client_token', $token)
            ->first();

        if ($exists) {
            DB::table('news_likes')->where('id', $exists->id)->delete();
            DB::table('news')->where('id', $id)->decrement('likes_count');
            $liked = false;
        } else {
            DB::table('news_likes')->insert([
                'news_id' => $id,
                'client_token' => $token,
                'created_at' => now(),
            ]);
            DB::table('news')->where('id', $id)->increment('likes_count');
            $liked = true;
        }

        $count = DB::table('news')->where('id', $id)->value('likes_count');
        return response()->json(['success' => true, 'news_id' => $id, 'liked' => $liked, 'likes_count' => $count]);
    }

    public function about()
    {
        return response('Новостной портал с AJAX-счётчиком Like (Laravel)', 200);
    }

    public function adminIndex()
    {
        return response()->json(['admin_news' => DB::table('news')->get()]);
    }

    public function store(Request $request)
    {
        $id = DB::table('news')->insertGetId($request->only(['title', 'summary', 'content']));
        return response()->json(['created_id' => $id], 201);
    }

    public function update(Request $request, int $id)
    {
        DB::table('news')->where('id', $id)->update($request->only(['title', 'summary', 'content']));
        return response()->json(['updated_id' => $id]);
    }

    public function destroy(int $id)
    {
        DB::table('news')->where('id', $id)->delete();
        return response()->json(['deleted_id' => $id]);
    }
}
