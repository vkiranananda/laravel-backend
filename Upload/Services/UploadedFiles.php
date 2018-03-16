<?php
namespace Backend\Root\Upload\Services;
use \Backend\Root\Upload\Models\MediaFile;
use Content;
use Helpers;

class UploadedFiles {

    private $images = [];
    private $reqFiles = [];
    private $reqImgSize = false;
    private $reqResultArray = true; 

    //Геренрим линки на файл
    public function genFileLink(&$file, $sizes = false)
    {  
        $size = \Backend\Root\Upload\Services\Uploads::sizesToStr($sizes);

        $res = [ 'orig' => $file['url'].$file['path'].urlencode($file['file'] ) ];

        if( $file['file_type'] == 'image' ) {
			if( pathinfo($file['file'])['extension'] == 'gif' || !$sizes ) {
            	$res['thumb'] = $orig;
            } else {
	            if(! isset($file['sizes'][$size])){
	                if(!is_array($file['sizes']))$file['sizes'] = [];
	                
	                $file['sizes'] = array_merge($file['sizes'], \Backend\Root\Upload\Services\Uploads::genSizes($file, [ $sizes ]));
	                
	                $file->save();
	            }

	            $res['thumb'] = $file['url'].$file['path'].$file['sizes'][$size]['path'].urlencode($file['sizes'][$size]['file']);
	        }
        } 
        return $res;
    }

    //Получаем и Сохраняем файлы в массив
    private function _getFiles($ids)
    {
        if(is_array($ids) && count($ids) > 0){

        	//Формируем новый массив на выборку, если файла нет в общем массиве
        	$idsReq = [];
        	foreach ($ids as $id) {
        		if( ! isset($this->images[$id]) )$idsReq[] = $id;
        	}
        	if(count($idsReq) > 0){
	            foreach (MediaFile::whereIn('id', $idsReq)->get() as $img) {
	                $this->images[$img['id']] = $img;
	            }
	        }
        }
    }

    //Получить урл миниатюры
    // public function getImgUrl(&$post, &$field, $sizes = [])
    // {
    //     if( isset($post['array_data']['fields'][$field]) ){
    //         $gal = $post['array_data']['fields'][$field];
    //         if(is_array($gal) && count($gal) > 0){
    //             $imgId = reset($gal);

    //             if(! isset($this->images[$imgId])){
    //                 if( ($img = MediaFile::find($imgId)) ){
    //                     $this->images[$imgId] = $img;
    //                     // $this->queryId[] = $imgId;
    //                 }else {
    //                     return '';
    //                 }
    //             }
    //             // $this->queryId[] = $imgId;
    //             return $this->genImgLink($this->images[$imgId], $sizes)['thumb'];
    //         }
    //     }
    //     return '';
    // }
    //Получить урлы по массиву
    // public function getImgUrlArr(&$post, &$field, $sizes = [])
    // {
    //     if( isset($post['array_data']['fields'][$field]) ){
    //         $gal = $post['array_data']['fields'][$field];
    //         if($this->_getFiles($gal)){
    //             $res = [];
    //             foreach ($gal as $imgId) {
    //                 if(isset($this->images[$imgId]))$res[] = $this->genImgLink($this->images[$imgId],$sizes);
    //             }   
    //             return $res;
    //         }
    //     }
    //     return [];
    // }

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
        $this->_getFiles($req);
        // dd($this->images);
    }

    //Формируем вывод для галерей
    public function prepGaleryData(&$list)
    {
    	$res = [];
        foreach ($list as $key => $file) {
			$res[] = array_merge(
				( ($file['file_type'] == 'image') 
					? $this->genFileLink($file, [128, 128, 'fit']) 
					: ['orig' => $this->genFileLink($file)['orig'], 'thumb' => '/images/system/file.png' ]),
				Helpers::setArray($file, ['id', 'orig_name', 'file_type'])
			);
        }
        return $res;
    }


    //getFirst
    //uFile->get($post, 'gallery')->size([128, 128, 'fit'])->htmlImg(['class' => 'thumb']);

    // Инитим данные из поля, для выборки массива
    public function get(&$post, $field)
    {
    	$this->reqImgSize = false;
    	$this->reqResultArray = true;
    	$this->reqFiles = Helpers::dataIsSetValue($post, $field);
    	if(!is_array($this->reqFiles)) $this->reqFiles = [];

        return $this;
    }

    // Инитим данные из поля, для выборки первого элемента
    public function getFirst(&$post, $field)
    {
    	$this->reqImgSize = false;
    	$this->reqResultArray = false;

    	$files = Helpers::dataIsSetValue($post, $field);
    	if (is_array($files) && count($files) > 0) {
    		$this->reqFiles = [ reset($files) ];
    	} else $this->reqFiles = [];

    	return $this;
    }

    //Устанавливает размер возвращаемой миниатюры. Только для картинок.
    public function size($size)
    {
    	$this->reqImgSize = $size;

    	return $this;
    }


    //Получаем готовый тэг html img, только для картинок, если вызван метод size будут сгенереный нужные размеры
    public function htmlImg($attr = [])
    {
    	$this->_getFiles($this->reqFiles);

    	$res = [];
		foreach ($this->reqFiles as $id) {
			if(!isset($this->images[$id]) || $this->images[$id]['file_type'] != 'image' ) continue;

			$attrNew = $attr;

			if (!isset($attr['title'])) 
	    		$attrNew['title'] = Helpers::dataIsSetValue($this->images[$id], 'img_title');
	    	if (!isset($attr['alt'])) 
	    		$attrNew['alt'] = Helpers::dataIsSetValue($this->images[$id], 'img_alt');

			$res[] = '<img src="'.$this->genFileLink($this->images[$id], $this->reqImgSize)['thumb'].'" '.Helpers::getAttrs($attrNew).'>';
		}
		if($this->reqResultArray) return $res;
		elseif(count($res) > 0) return $res[0];

		return '';

    }


    //Получить список урлов, если вызван метод size будут сгенереный нужные размеры(только для изображений)
    public function url()
    {
    	$this->_getFiles($this->reqFiles);

    	$res = [];

		foreach ($this->reqFiles as $id) {
			if(!isset($this->images[$id])) continue;

			$res[] = $this->genFileLink($this->images[$id], $this->reqImgSize);
		}

		if($this->reqResultArray) return $res;
		elseif(count($res) > 0) return $res[0];

		return [];
    }


    //Получить массив файлов
    public function files()
    {
    	$this->_getFiles($this->reqFiles);

    	$res = [];

		foreach ($this->reqFiles as $id) {
			if(!isset($this->images[$id])) continue;

			$res[] = &$this->images[$id];
		}

		if($this->reqResultArray) return $res;
		elseif(count($res) > 0) return $res[0];

		return [];
    }

}