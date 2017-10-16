<?php

namespace Backend\Upload\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Backend\Upload\Models\MediaFile;



class EditController extends Controller
{
    public function getInfo($id)
    {
        return MediaFile::findOrFail($id);
    }

    public function saveInfo(Request $request, $id)
    {
        MediaFile::where('id', $id)->update( ['desc' => substr(htmlspecialchars($request->input('desc', '') ), 0, 250 ) ] );
    }


    // public function test()
    // {
    // 	$mf = MediaFile::findOrFail(281);
    // //	for ($i=0; $i < 10; $i++) { 
    // 		\Backend\Upload\Services\Uploads::genSizes($mf, [ [200, 200, 'fit'], [200, 200, 'fit'],[200, 200, 'fit'],[200, 200, 'fit'],[200, 200, 'fit'],[200, 200, 'fit'],[200, 200, 'fit'],[200, 200, 'fit'],[200, 200, 'fit'],[200, 200, 'fit']  ]);
    // //	}

    // 	printf('Скрипт выполнялся %.4F сек.', (microtime(true) - $GLOBALS['START-TIME']));
    //     return "eee";
    // }
}
