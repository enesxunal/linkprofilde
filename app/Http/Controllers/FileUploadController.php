<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic as Image;

class FileUploadController extends Controller
{
    function SingleUpload(Request $req)
    {
        $location = public_path('/upload/');
        $image = Image::make($req->image);
        $image->save($location.time().$req->image->getClientOriginalName());
        $imgUrl = 'upload/'.$image->filename.'.'.$image->extension;

        return $imgUrl;        
    }

    function SingleFileUpload(Request $req, $prevImage)
    {
        if (File::exists("upload/{$prevImage}")) {
            File::delete("upload/{$prevImage}");
        }

        $location = public_path('/upload/');
        $image = Image::make($req->image);
        $image->save($location.time().$req->image->getClientOriginalName());
        $imgUrl = 'upload/'.$image->filename.'.'.$image->extension;

        return $imgUrl;        
    }
}
