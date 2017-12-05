<?php

namespace Backend\Root\Upload\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Backend\Root\Upload\Models\MediaFile;



class EditController extends Controller
{
    public function getInfo($id)
    {
        return MediaFile::findOrFail($id);
    }

    public function saveInfo(Request $request)
    {
        $save = [];
        foreach (['desc', 'img_title', 'img_alt'] as $field) {
            $save[$field] = substr(htmlspecialchars($request->input($field, '') ), 0, 250 );
        }
        print_r($save);
        MediaFile::where('id', $request->input('id', ''))->update( $save );
    }

}
