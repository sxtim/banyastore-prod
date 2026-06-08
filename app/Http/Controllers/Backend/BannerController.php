<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\ActionRequest;
use App\Http\Requests\Backend\BannerRequest;
use App\Models\Action;
use App\Models\Banner;
use App\Services\BannerImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\File;
use Illuminate\Http\RedirectResponse;

class BannerController extends Controller
{
    const DIR_BANNERS_MAIN = 'public/banners/main/';

    public function __construct(private BannerImageService $bannerImageService)
    {
    }

    public function index(): View
    {
        $banners = Banner::paginate(15);
        return view('backend.banner.index', compact('banners'));
    }

    public function create(): View
    {
        return view('backend.banner.create');
    }

    public function edit(int $bannerId): View
    {
        $banner = Banner::find($bannerId);
        return view('backend.banner.edit',
            compact('banner')
        );
    }

    public function store(BannerRequest $request): RedirectResponse
    {
        $image = $request->file('image');
        if ($image) {
            $extension = $image->getClientOriginalExtension();
            $filenameMainImg = uniqid() . '.' . $extension;
            Storage::put(self::DIR_BANNERS_MAIN . $filenameMainImg, File::get($image));
            $this->bannerImageService->safelyGenerateResponsiveCopies(self::DIR_BANNERS_MAIN . $filenameMainImg);
        }

        Banner::create([
            'name' => $request->input('name'),
            'link' => $request->input('link'),
            'sort' => $request->input('sort'),
            'is_active' => $request->boolean('is_active'),
            'image' => $image ? self::DIR_BANNERS_MAIN . $filenameMainImg : '',
        ]);

        return redirect()->route('backend.banner.index')->with('success', 'Изменения сохранены');
    }

    public function update(int $bannerId, BannerRequest $request): RedirectResponse
    {
        $banner = Banner::findOrFail($bannerId);

        $data = [
            'name' => $request->input('name'),
            'link' => $request->input('link'),
            'sort' => $request->input('sort'),
            'is_active' => $request->boolean('is_active'),
        ];

        $image = $request->file('image');
        if ($image) {
            $this->bannerImageService->deleteResponsiveCopies($banner->image);
            Storage::delete($banner->image);
            $extension = $image->getClientOriginalExtension();
            $filenameMainImg = uniqid() . '.' . $extension;
            Storage::put(self::DIR_BANNERS_MAIN . $filenameMainImg, File::get($image));
            $data['image'] = self::DIR_BANNERS_MAIN . $filenameMainImg;
            $this->bannerImageService->safelyGenerateResponsiveCopies($data['image']);
        }

        $banner->update($data);

        return redirect()->route('backend.banner.index')->with('success', 'Изменения сохранены');
    }
}
