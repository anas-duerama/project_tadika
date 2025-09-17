<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    private array $categories = ['ประกาศ','กิจกรรม','รับสมัคร','ข่าวทั่วไป','ประชาสัมพันธ์'];

    public function index() {
        $news = News::latest('published_at')->latest()->paginate(10);
        return view('admin.news.index', compact('news'));
    }

    public function create() {
        $categories = $this->categories;
        return view('admin.news.create', compact('categories'));
    }

    public function store(StoreNewsRequest $request) {
        $data = $request->validated();
        if ($request->hasFile('cover')) {
            $data['cover_path'] = $request->file('cover')->store('news_covers', 'public');
        }
        $data['author_id'] = auth()->id();
        News::create($data);
        return redirect()->route('admin.news.index')->with('success','เพิ่มข่าวสารเรียบร้อย');
    }

    public function edit(News $news) {
        $categories = $this->categories;
        return view('admin.news.edit', compact('news','categories'));
    }

    public function update(UpdateNewsRequest $request, News $news) {
        $data = $request->validated();
        if ($request->hasFile('cover')) {
            if ($news->cover_path) Storage::disk('public')->delete($news->cover_path);
            $data['cover_path'] = $request->file('cover')->store('news_covers', 'public');
        }
        $news->update($data);
        return redirect()->route('admin.news.index')->with('success','แก้ไขข่าวสารเรียบร้อย');
    }

    public function destroy(News $news) {
        if ($news->cover_path) Storage::disk('public')->delete($news->cover_path);
        $news->delete();
        return redirect()->route('admin.news.index')->with('success','ลบข่าวสารเรียบร้อย');
    }
}
