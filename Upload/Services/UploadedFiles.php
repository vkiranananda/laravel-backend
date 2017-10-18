<?php
namespace Backend\Root\Upload\Services;
use \Backend\Root\Upload\Models\MediaFile;

class UploadedFiles {

    private $images = [];

    public function genFileLink(&$file)
    {
        return url($file['url'].$file['path'].$file['file']);
    }

    //Геренрим линки на миниатюры
    public function genImgLink(&$img, &$sizes)
    {  
        $size = \Backend\Root\Upload\Services\Uploads::sizesToStr($sizes);

        $orig = url ($img['url'].$img['path'].urlencode($img['file'] ));

        if( pathinfo($img['file'])['extension'] == 'gif' ) {
            $thumb = $orig;
        }else{
            if(! isset($img['sizes'][$size])){
                if(!is_array($img['sizes']))$img['sizes'] = [];
                
                $img['sizes'] = array_merge($img['sizes'], \Backend\Root\Upload\Services\Uploads::genSizes($img, [ $sizes ]));
                
                $img->save();
            }

            $thumb = url($img['url'].$img['path'].$img['sizes'][$size]['path'].urlencode($img['sizes'][$size]['file']));
        }

        return [ 'orig' => $orig, 'thumb' => $thumb ];
    }

    //Получаем и Сохраняем картикни в массив
    private function _getImages($ids)
    {
        if(is_array($ids) && count($ids) > 0){
            foreach (MediaFile::whereIn('id', $ids)->get() as $img) {
                $this->images[$img['id']] = $img;
            }
            // $this->queryId[] = $ids;
            return true;
        }
        return false;
    }

    //Получить урл миниатюры
    public function getImgUrl(&$post, &$field, $sizes = [])
    {
        if( isset($post['array_data']['fields'][$field]) ){
            $gal = $post['array_data']['fields'][$field];
            if(is_array($gal) && count($gal) > 0){
                $imgId = reset($gal);

                if(! isset($this->images[$imgId])){
                    if( ($img = MediaFile::find($imgId)) ){
                        $this->images[$imgId] = $img;
                        // $this->queryId[] = $imgId;
                    }else {
                        return '';
                    }
                }
                // $this->queryId[] = $imgId;
                return $this->genImgLink($this->images[$imgId], $sizes)['thumb'];
            }
        }
        return '';
    }
    //Получить урлы по массиву
    public function getImgUrlArr(&$post, &$field, $sizes = [])
    {
        if( isset($post['array_data']['fields'][$field]) ){
            $gal = $post['array_data']['fields'][$field];
            if($this->_getImages($gal)){
                $res = [];
                foreach ($gal as $imgId) {
                    if(isset($this->images[$imgId]))$res[] = $this->genImgLink($this->images[$imgId],$sizes);
                }   
                return $res;
            }
        }
        return [];
    }

    //Получаем все картинки оптом что бы не плодить запросы
    public function getImgUrlList(&$list, &$field)
    {
        $req = [];
        foreach ($list as $post) {
            if( isset($post['array_data']['fields'][$field]) ){
                $gal = $post['array_data']['fields'][$field];
                if(is_array($gal) && count($gal) > 0 && !isset( $this->images[reset($gal)] ) ){
                    $req[] = reset($gal);
                }
            }
        }
        $this->_getImages($req);
        // dd($this->images);
    }
}