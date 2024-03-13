<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\NewsRequest;
use App\Models\News;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\File;
use Illuminate\Http\RedirectResponse;

class NewsController extends Controller
{
    const DIR_NEWS_MAIN = 'public/news/main/';
    const DIR_NEWS_PREVIEW = 'public/news/preview/';
    const DIR_OTHER_IMAGES_NEWS = 'public/news/other/';

    public function index(): View
    {
        $news = News::paginate(15);
        return view('backend.news.index', compact('news'));
    }

    public function create(): View
    {
        return view('backend.news.create');
    }

    public function edit(int $newsId): View
    {
        $news = News::find($newsId);
        return view('backend.news.edit',
            compact('news')
        );
    }

    public function store(NewsRequest $request): RedirectResponse
    {
        $mainImg = $request->file('main_image');
        if ($mainImg) {
            $extension = $mainImg->getClientOriginalExtension();
            $filenameMainImg = uniqid() . '.' . $extension;
            Storage::put(self::DIR_NEWS_MAIN . $filenameMainImg, File::get($mainImg));
        }
        $previewImg = $request->file('preview_image');
        if ($previewImg) {
            $extension = $previewImg->getClientOriginalExtension();
            $filenamePreview = uniqid() . '.' . $extension;
            Storage::put(self::DIR_NEWS_PREVIEW . $filenamePreview, File::get($previewImg));
        }

        News::create([
            'name' => $request->input('name'),
            'sort' => $request->input('sort'),
            'is_active' => $request->boolean('is_active'),
            'preview_img' => $previewImg ? self::DIR_NEWS_PREVIEW . $filenamePreview : '',
            'main_img' => $mainImg ? self::DIR_NEWS_MAIN . $filenameMainImg : '',
            'preview_text' => $request->input('preview_text'),
            'detail_text' => json_decode($request->input('detail_text'), true),
            'btn' => $request->input('btn') ?? '',
            'link_btn' => $request->input('link_btn')  ?? '',
            'start_at' => $request->input('start_at'),
            'end_at' => $request->input('end_at'),
        ]);

        return redirect()->route('backend.news.index')->with('success', 'Изменения сохранены');
    }

    public function update(int $newsId, NewsRequest $request): RedirectResponse
    {
        $news = News::findOrFail($newsId);

        $data = [
            'name' => $request->input('name'),
            'sort' => $request->input('sort'),
            'is_active' => $request->boolean('is_active'),
            'preview_text' => $request->input('preview_text'),
            'detail_text' => json_decode($request->input('detail_text'), true),
            'btn' => $request->input('btn') ?? '',
            'link_btn' => $request->input('link_btn') ?? '',
            'start_at' => $request->input('start_at'),
            'end_at' => $request->input('end_at'),
        ];

        $mainImg = $request->file('main_image');
        if ($mainImg) {
            Storage::delete($news->main_img);
            $extension = $mainImg->getClientOriginalExtension();
            $filenameMainImg = uniqid() . '.' . $extension;
            Storage::put(self::DIR_NEWS_MAIN . $filenameMainImg, File::get($mainImg));
            $data['main_img'] = self::DIR_NEWS_MAIN . $filenameMainImg;
        }
        $previewImg = $request->file('preview_image');
        if ($previewImg) {
            Storage::delete($news->preview_img);
            $extension = $previewImg->getClientOriginalExtension();
            $filenamePreview = uniqid() . '.' . $extension;
            Storage::put(self::DIR_NEWS_PREVIEW . $filenamePreview, File::get($previewImg));
            $data['preview_img'] = self::DIR_NEWS_PREVIEW . $filenamePreview;
        }

        $news->update($data);

        return redirect()->route('backend.news.index')->with('success', 'Изменения сохранены');
    }

    public function addImage(Request $request): JsonResponse
    {
        $extension = $request->file('image')->getClientOriginalExtension();
        $filename = uniqid() . '.' . $extension;
        $pathImage = self::DIR_OTHER_IMAGES_NEWS . $filename;
        Storage::put($pathImage, File::get($request->file('image')));

        return response()->json([
            'file' => [
                'url' => Storage::url($pathImage),
                'filePath' => $pathImage,
            ]
        ]);
    }

    public function deleteImage(Request $request): void
    {
        Storage::delete($request->input('file'));
    }
}
