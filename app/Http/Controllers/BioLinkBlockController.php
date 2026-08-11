<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Support\BioItemLink;
use Illuminate\Http\Request;
use App\Models\LinkItem;
use Illuminate\Validation\ValidationException;

class BioLinkBlockController extends Controller
{
    //--------------------------------------------------------
    // Add new element of bio-link
    public function add(Request $req)
    {
        $link = AppHelper::get_link((int) $req->link_id);
        if (!$link) {
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
            $item->link_id = (int) $req->link_id;
            $item->item_position = (int) $req->item_position;
            $item->item_type = $req->item_type;
            $item->item_sub_type = $req->item_sub_type == "null" ? NULL : $req->item_sub_type;
            $item->item_title = $req->item_title;
            $item->item_link = $itemLink;
            $item->item_icon = $req->item_icon;

            if ($req->hasFile('image')) {
                $item->content = AppHelper::image_uploader($req->file('image'));
            } else {
                $item->content = $this->nonFileContent($req->content);
            }
            $item->save();
            $link = AppHelper::get_link($req->link_id);

            return response()->json(['success' => true, 'item' => $item, 'link' => $link]);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => collect($e->errors())->flatten()->first() ?? 'Geçersiz veri.',
            ]);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    //--------------------------------------------------------


    //--------------------------------------------------------
    // Updating an element of bio-link
    public function edit(Request $req, $id)
    {
        $item = LinkItem::find($id);
        if (!$item) {
            abort(404);
        }
        $link = AppHelper::get_link($item->link_id);
        if (!$link) {
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
            $item->item_sub_type = $req->item_sub_type == "null" ? NULL : $req->item_sub_type;
            $item->item_title = $req->item_title;
            $item->item_link = $itemLink;

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
            $link = AppHelper::get_link($req->link_id);

            return response()->json(['success' => true, 'link' => $link]);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => collect($e->errors())->flatten()->first() ?? 'Geçersiz veri.',
            ]);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    //--------------------------------------------------------


    //--------------------------------------------------------
    // Updating the position of bio-link elements when user drag and drop on view.
    function position(Request $req, $id)
    {
        $link = AppHelper::get_link($id);
        if (!$link) {
            abort(403, 'Yetkisiz erişim.');
        }

        try {
            $linkItems = $req->input('linkItems');
            $newArr = json_decode(json_encode($linkItems));
            foreach ($newArr as $item) {
                LinkItem::where('id', $item->id)->update([
                    'item_position' => $item->position
                ]);
            }

            $link = AppHelper::get_link($id);
            return response()->json(['success' => true, 'link' => $link]);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    //--------------------------------------------------------


    //--------------------------------------------------------
    // Delete an element of bio-link
    function delete($id)
    {
        $item = LinkItem::find($id);
        if (!$item) {
            abort(404);
        }
        $link = AppHelper::get_link($item->link_id);
        if (!$link) {
            abort(403, 'Yetkisiz erişim.');
        }

        try {
            $link_id = $item->link_id;

            if ($item->item_type == 'Image') {
                AppHelper::safeDeleteUpload($item->content);
            }
            $item->delete();

            $link = AppHelper::get_link($link_id);

            return response()->json(['success' => true, 'link' => $link]);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
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
