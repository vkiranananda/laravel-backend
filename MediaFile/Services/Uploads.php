<?php
namespace Backend\Root\MediaFile\Services;

use Storage;
use Request;
use Auth;
use Backend\Root\MediaFile\Models\MediaFile;
use Intervention\Image\Facades\Image as Image;

class Uploads {

    public static function saveFile($conf)
    {
        setlocale(LC_ALL, 'ru_RU.utf8');

        $file = Request::file('file');
        $mediaFile = new MediaFile;
      
        $mediaFile->disk = $conf['disk'];
        $mediaFile->type = $conf['store-type'];
        $mediaFile->url = $conf['url'];        
        $mediaFile->user_id = ( isset($conf['user-id']) ) ? $conf['user-id'] : Auth::user()->id;
        $mediaFile->orig_name = $file->getClientOriginalName();
        $mediaFile->imageable_type = (isset($conf['module'])) ? $conf['module'] : '';

        $array_data['mime'] = $file->getClientMimeType();

        $mediaFile->save();

        $mediaFile->path = '000'.dechex($mediaFile->id);
        $mediaFile->path = mb_substr($mediaFile->path, -4, 2).'/'.mb_substr($mediaFile->path, -2, 2).'/';
        // $mediaFile->path = 'test/'; //Удалить когда протестируем..

        $fileInfo = pathinfo($file->getClientOriginalName());
        if(!isset($fileInfo['extension'])) $fileInfo['extension'] = '';

        //Устанавливаем имя файлу
        if($conf['file-name-type'] == 'id'){
            $fileName = $mediaFile->id;
            if($fileInfo['extension'] != '') $fileName .= '.'.$fileInfo['extension'];
        } else {
            $fileName = mb_substr(
                str_replace(' ', '-', 
                    str_replace( [ '\\','/','<','>','%','?',':','*','"','|','+','!','@' ], '', $file->getClientOriginalName() )
                )
                  , -50, 50);
        }

        //Подбираем индивидуальное имя
        $mediaFile->file = Uploads::getIndividualName($mediaFile, $fileName );

        //Генерируем миниатюру
        if( array_search(strtolower($fileInfo['extension']), ['jpeg', 'png', 'gif', 'jpg'], true) !== false ){
            
            $mediaFile->file_type = 'image';

            if( isset($conf['sizes']) && is_array($conf['sizes']) && count($conf['sizes']) > 0 ){
                $mediaFile->sizes = Uploads::genSizes($mediaFile, $conf['sizes'], $file->getPathname());
            } 
        }

        //Сохраняем файл
        $file->storeAs($mediaFile->path, $mediaFile->file, $mediaFile->disk);
        
        $mediaFile->array_data = $array_data;
        
        $mediaFile->save();

        return $mediaFile;
    }


    //Преобразуем массив с размеров в строку...
    public static function sizesToStr($size){
    	if(count($size) < 2) return '';
        $res = $size[0]."x".$size[1];
        $res .= (isset($size[2]) && $size[2] == 'fit') ? '-fit' : '' ;
        return $res;
    }

    //Генерируем различные размеры
    public static function genSizes(&$file, $sizes, $tmpFile = false)
    {
        $res = array();
        $disk = Storage::disk($file['disk']);
        $orig = false;

        $loadedFile = ($tmpFile) ? file_get_contents($tmpFile) : $disk->get($file['path'].$file['file']);

        foreach ($sizes as $value) 
        {
            $img = Image::make($loadedFile);
            
            //Оригинальные размеры, что бы потом можно было проверить был ли изменен файл
            if($orig === false) {
                if( !isset( $file['sizes']['orig'] ) ){
                    $res['orig']['size'] = [ $img->width(), $img->height()];
                    $res['orig']['file'] = $file['file'];
                    $res['orig']['path'] = '/';
                    $orig = $res['orig'];
                }else {
                    $orig = $file['sizes']['orig'];
                }
            }

            $sizeStr = Uploads::sizesToStr($value);
            if(isset($value[2]) && $value[2] == 'fit'){
                $img->fit($value[0], $value[1], function ($constraint) {
                    $constraint->upsize();  
                });
            }else{
                if($value[0] == 'auto')$value[0] = null;
                if($value[1] == 'auto')$value[1] = null;
                $img->resize($value[0], $value[1], function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                });                    
            }

            //Если файл не был изменен не сохраяем его...
            if(($img->width() == $orig['size'][0] ) && ($img->height() == $orig['size'][1]) ){
                $res[$sizeStr] = $orig;
                continue;
            }

            $res[$sizeStr ]['size'] = [ $img->width(), $img->height() ];

            //Сохраняем только в png формате
            $res[$sizeStr ]['path'] = 'sizes/'.$sizeStr.'/';
            $res[$sizeStr ]['file'] = pathinfo($file['file'])['filename'].'.png';

            $disk->put($file['path'].$res[$sizeStr ]['path'].$res[$sizeStr ]['file'], $img->encode('png') );
        }

        return $res;
    }

    //получаем индивидуальное имя. Можно вообще брать ID записи и сохранять под ним, будет быстрее и проще и не надо ничего проверять :)
    private static function getIndividualName(&$newFile, $name)
    {
        $fInfo = pathinfo($name);
        
        $fInfo['extension'] = (isset($fInfo['extension'])) ? '.'.$fInfo['extension'] : '' ;

        //Получаем список файлов в каталоге
        $filesExists = [];
        foreach ( MediaFile::where('disk', $newFile['disk'])->where('path', $newFile['path'])->get(['file']) as $file ) {
            $filesExists[$file['file']] = '';
        }
        $index = 1;

        //Ищем подходящее имя файла
        while( isset($filesExists[$name]) )
        {
            $name = $fInfo['filename']."-".$index++.$fInfo['extension'];
        }

        return $name;
    }

    //Удаляет массив файлов
    public static function deleteFiles($files){
        foreach ($files as $file) {
        	//Удаляем основной файл
            Storage::disk( $file['disk'] )->delete($file['path'].$file['file']);

            if(! is_array($file['sizes']))continue;
            // Удаляем миниатюры
            foreach ($file['sizes'] as $fileSizes) {
                Storage::disk( $file['disk'] )->delete($file['path'].$fileSizes['path'].$fileSizes['file']);
            }
            //Удаляем из базы
            MediaFile::destroy($file['id']);
        }
    }
}

?>