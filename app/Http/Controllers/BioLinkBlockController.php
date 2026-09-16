<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\LinkItem;
use App\Support\BioItemLink;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BioLinkBlockController extends Controller
{
    //--------------------------------------------------------
    // Add new element of bio-link
    public function add(Request $req)
    {
        $req->validate([
            'link_id' => ['required', 'integer'],
            'item_position' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'item_type' => ['required', 'string', 'max:50'],
            'item_sub_type' => ['nullable', 'string', 'max:50'],
            'item_title' => ['nullable', 'string', 'max:255'],
            'item_link' => ['nullable', 'string', 'max:2000'],
            'item_icon' => ['nullable', 'string', 'max:100'],
            'content' => ['nullable', 'string', 'max:10000'],
        ]);

        $link = AppHelper::get_link((int) $req->link_id);
        if (! $link) {
            abort(403, 'Yetkisiz erişim.');
        }

        if ($req->hasFile('image')) {
            $req->validate([
                'image' => AppHelper::imageRules(2048),
            ]);
        }

        try {
            $itemLink = BioItemLink::normalize(
                is_string($req->item_link) ? $req->item_link : null,
                is_string($req->item_type) ? $req->item_type : null,
                is_string($req->item_icon) ? $req->item_icon : null
            );

            $item = new LinkItem;
            $item->link_id = (int) $link->id;
            $item->item_position = (int) ($req->item_position ?? 0);
            $item->item_type = $req->item_type;
            $item->item_sub_type = $req->item_sub_type === 'null' ? null : $req->item_sub_type;
            $item->item_title = $req->item_title;
            $item->item_link = $itemLink;
            $item->item_icon = $req->item_icon;

            if ($req->hasFile('image')) {
                $item->content = AppHelper::image_uploader($req->file('image'));
            } else {
                $item->content = $this->nonFileContent($req->content);
            }
            $item->save();

            $updatedLink = AppHelper::get_link($link->id);

            return response()->json(['success' => true, 'item' => $item, 'link' => $updatedLink]);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => collect($e->errors())->flatten()->first() ?? 'Geçersiz veri.',
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => AppHelper::publicExceptionMessage($th),
            ], 422);
        }
    }
    //--------------------------------------------------------

    //--------------------------------------------------------
    // Updating an element of bio-link
    public function edit(Request $req, $id)
    {
        $req->validate([
            'item_type' => ['required', 'string', 'max:50'],
            'item_sub_type' => ['nullable', 'string', 'max:50'],
            'item_title' => ['nullable', 'string', 'max:255'],
            'item_link' => ['nullable', 'string', 'max:2000'],
            'item_icon' => ['nullable', 'string', 'max:100'],
            'content' => ['nullable', 'string', 'max:10000'],
        ]);

        $item = LinkItem::find($id);
        if (! $item) {
            abort(404);
        }

        $link = AppHelper::get_link($item->link_id);
        if (! $link) {
            abort(403, 'Yetkisiz erişim.');
        }

        if ($req->hasFile('image')) {
            $req->validate([
                'image' => AppHelper::imageRules(2048),
            ]);
        }

        try {
            $itemLink = BioItemLink::normalize(
                is_string($req->item_link) ? $req->item_link : null,
                is_string($req->item_type) ? $req->item_type : (string) $item->item_type,
                is_string($req->item_icon) ? $req->item_icon : (string) $item->item_icon
            );

            $item->item_type = $req->item_type;
            $item->item_sub_type = $req->item_sub_type === 'null' ? null : $req->item_sub_type;
            $item->item_title = $req->item_title;
            $item->item_link = $itemLink;
            $item->item_icon = $req->item_icon;

            if ($req->hasFile('image')) {
                AppHelper::safeDeleteUpload($item->content);
                $item->content = AppHelper::image_uploader($req->file('image'));
            } else {
                $incoming = $this->nonFileContent($req->content);
                if ($incoming !== null) {
                    $item->content = $incoming;
                }
            }
            $item->save();

            $updatedLink = AppHelper::get_link($link->id);

            return response()->json(['success' => true, 'link' => $updatedLink]);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => collect($e->errors())->flatten()->first() ?? 'Geçersiz veri.',
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => AppHelper::publicExceptionMessage($th),
            ], 422);
        }
    }
    //--------------------------------------------------------

    //--------------------------------------------------------
    // Updating the position of bio-link elements when user drag and drop on view.
    public function position(Request $req, $id)
    {
        $req->validate([
            'linkItems' => ['required', 'array', 'max:200'],
            'linkItems.*.id' => ['required', 'integer'],
            'linkItems.*.position' => ['required', 'integer', 'min:0', 'max:10000'],
        ]);

        $link = AppHelper::get_link($id);
        if (! $link) {
            abort(403, 'Yetkisiz erişim.');
        }

        try {
            foreach ($req->input('linkItems', []) as $item) {
                $updated = LinkItem::query()
                    ->where('id', (int) $item['id'])
                    ->where('link_id', $link->id)
                    ->update(['item_position' => (int) $item['position']]);

                if ($updated !== 1) {
                    throw ValidationException::withMessages([
                        'linkItems' => 'Geçersiz veya bu profile ait olmayan bir blok gönderildi.',
                    ]);
                }
            }

            $updatedLink = AppHelper::get_link($link->id);

            return response()->json(['success' => true, 'link' => $updatedLink]);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => collect($e->errors())->flatten()->first() ?? 'Geçersiz veri.',
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => AppHelper::publicExceptionMessage($th),
            ], 422);
        }
    }
    //--------------------------------------------------------

    //--------------------------------------------------------
    // Delete an element of bio-link
    public function delete($id)
    {
        $item = LinkItem::find($id);
        if (! $item) {
            abort(404);
        }

        $link = AppHelper::get_link($item->link_id);
        if (! $link) {
            abort(403, 'Yetkisiz erişim.');
        }

        try {
            $linkId = $item->link_id;

            if ($item->item_type === 'Image') {
                AppHelper::safeDeleteUpload($item->content);
            }
            $item->delete();

            $updatedLink = AppHelper::get_link($linkId);

            return response()->json(['success' => true, 'link' => $updatedLink]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => AppHelper::publicExceptionMessage($th),
            ], 422);
        }
    }
    //--------------------------------------------------------

    private function nonFileContent($content): ?string
    {
        if ($content === null || $content === 'null' || $content === '') {
            return null;
        }

        $normalized = str_replace('\\', '/', trim((string) $content));
        if (
            str_contains($normalized, '..')
            || str_starts_with($normalized, '/')
            || str_starts_with($normalized, 'upload/')
            || str_contains($normalized, '://')
            || preg_match('/^[a-zA-Z]:\//', $normalized)
        ) {
            return null;
        }

        return (string) $content;
    }
}
