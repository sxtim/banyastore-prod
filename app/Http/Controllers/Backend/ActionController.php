<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\ActionRequest;
use App\Models\Action;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\File;
use Illuminate\Http\RedirectResponse;

class ActionController extends Controller
{
    const DIR_ACTIONS_MAIN = 'public/actions/main/';
    const DIR_ACTIONS_PREVIEW = 'public/actions/pr/';
    const DIR_OTHER_IMAGES_ACTIONS = 'public/actions/other/';

    public function index(): View
    {
        $actions = Action::paginate(15);
        return view('backend.actions.index', compact('actions'));
    }

    public function create(): View
    {
        return view('backend.actions.create');
    }

    public function edit(int $actionId): View
    {
        $action = Action::find($actionId);
        return view('backend.actions.edit',
            compact('action')
        );
    }

    public function store(ActionRequest $request): RedirectResponse
    {
        $mainImg = $request->file('main_image');
        if ($mainImg) {
            $extension = $mainImg->getClientOriginalExtension();
            $filenameMainImg = uniqid() . '.' . $extension;
            Storage::put(self::DIR_ACTIONS_MAIN . $filenameMainImg, File::get($mainImg));
        }
        $previewImg = $request->file('preview_image');
        if ($previewImg) {
            $extension = $previewImg->getClientOriginalExtension();
            $filenamePreview = uniqid() . '.' . $extension;
            Storage::put(self::DIR_ACTIONS_PREVIEW . $filenamePreview, File::get($previewImg));
        }

        Action::create([
            'name' => $request->input('name'),
            'sort' => $request->input('sort'),
            'is_active' => $request->boolean('is_active'),
            'preview_img' => $previewImg ? self::DIR_ACTIONS_PREVIEW . $filenamePreview : '',
            'main_img' => $mainImg ? self::DIR_ACTIONS_MAIN . $filenameMainImg : '',
            'preview_text' => $request->input('preview_text'),
            'detail_text' => json_decode($request->input('detail_text'), true),
            'btn' => $request->input('btn') ?? '',
            'link_btn' => $request->input('link_btn')  ?? '',
            'start_at' => $request->input('start_at'),
            'end_at' => $request->input('end_at'),
        ]);

        return redirect()->route('backend.actions.index')->with('success', 'Изменения сохранены');
    }

    public function update(int $actionId, ActionRequest $request): RedirectResponse
    {
        $action = Action::findOrFail($actionId);

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
            Storage::delete($action->main_img);
            $extension = $mainImg->getClientOriginalExtension();
            $filenameMainImg = uniqid() . '.' . $extension;
            Storage::put(self::DIR_ACTIONS_MAIN . $filenameMainImg, File::get($mainImg));
            $data['main_img'] = self::DIR_ACTIONS_MAIN . $filenameMainImg;
        }
        $previewImg = $request->file('preview_image');
        if ($previewImg) {
            Storage::delete($action->preview_img);
            $extension = $previewImg->getClientOriginalExtension();
            $filenamePreview = uniqid() . '.' . $extension;
            Storage::put(self::DIR_ACTIONS_PREVIEW . $filenamePreview, File::get($previewImg));
            $data['preview_img'] = self::DIR_ACTIONS_PREVIEW . $filenamePreview;
        }

        $action->update($data);

        return redirect()->route('backend.actions.index')->with('success', 'Изменения сохранены');
    }

    public function addImage(Request $request): JsonResponse
    {
        $extension = $request->file('image')->getClientOriginalExtension();
        $filename = uniqid() . '.' . $extension;
        $pathImage = self::DIR_OTHER_IMAGES_ACTIONS . $filename;
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
