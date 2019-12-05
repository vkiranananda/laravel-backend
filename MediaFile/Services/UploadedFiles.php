<?php
namespace Backend\Root\MediaFile\Services;
use \Backend\Root\MediaFile\Models\MediaFile;
use \Backend\Root\MediaFile\Services\Uploads;
use Content;
use Helpers;
use Log;
class UploadedFiles {

	// Массив с изображениями
    private $images = [];
    // Запрошенные файлы в текущем запросе
    private $reqFiles = [];
    // Запрошенные размеры в текущем запросе
    private $reqImgSize = [];
    // Тип возвращаемого значения.
    private $reqResultArray = true; 

    // Прелоадинг файлов
    private $loadFiles = [];

    // Генерим миниатюрку к файлу пример: [100, 100, 'fit'], [100, 'auto']
    public function genFileLink($file, $sizes = [])
    {  
        if (count($sizes) > 0 && $file['file_type'] == 'image') {
			if (pathinfo($file['file'])['extension'] == 'gif') {
            	return $this->getOrigPath($file);
            } else {
            	$size = Uploads::sizesToStr($sizes);
	          
	          	// Если миниатюрки нет.
	            if (! isset($file['sizes'][$size])) {
	                if (!is_array($file['sizes'])) $file['sizes'] = [];
	                
	                $file['sizes'] = array_merge($file['sizes'], Uploads::genSizes($file, [ $sizes ]));
	                $file->save();
	            }
	            return $file['url'].$file['path'].$file['sizes'][$size]['path'].urlencode($file['sizes'][$size]['file']);
	        }
        } 
        return false;
    }

    private function getOrigPath($file) {
    	return $file['url'].$file['path'].urlencode($file['file'] );
    }

    // Получаем ранее иниченные файл и Сохраняем в массив картинок
    public function getFiles()
    {
    	// Формируем новый массив на выборку, если файла нет в общем массиве
    	$idsReq = [];
    	foreach ($this->loadFiles as $id) {
    		if( ! isset($this->images[$id]) ) $idsReq[$id] = $id;
    	}
    	if (count($idsReq) > 0){
            foreach (MediaFile::whereIn('id', $idsReq)->get() as $img) {
                $this->images[$img['id']] = $img;
            }
        }
        $this->loadFiles = [];
    }


    // Получаем все картинки в списке записей оптом, что бы не плодить запросы
    // Например нужно для списка элементов. Если $first == true, выбираем только первые картинки
    public function loadByList($list, $field, $first = true)
    {
        $req = [];
        foreach ($list as $post) {
        	$gal = Helpers::getDataField($post, $field, []);
            if (count($gal) > 0) {
            	// Только первые картинки
            	if ($first) $req[] = reset($gal);
            	else $req = array_merge($req, $gal); 
            }
        }

        $this->loadByArray($req);
    }

    // Добавляет данные для автоматической загрузки всех изображений, что бы не плодить запросы
    // указвается массив данных
    public function loadByArray($data) 
    {
    	if (is_array($data)) {
    		foreach ($data as $id) {
    			$this->loadFiles[$id] = $id;
    		}
    	}

    	return $this;
    }

    // Добавляет данные для автоматической загрузки всех изображений, что бы не плодить запросы
    // Можно указать как название поля, так и массив из названий поля
    public function loadByField($post, $field) 
    {
    	$fields = (!is_array($field)) ? [$field] : $field;

    	foreach ($fields as $field) {
    		$this->loadByArray(Helpers::getDataField($post, $field)); 
    	}

    	return $this;
    }

    // Добавляет данные для автоматической загрузки всех изображений, что бы не плодить запросы
    // указывается модель данных. Запрашиваются все файлы пренадлежашие этой модели.
    public function loadByModel($model) 
    {
    	$className = class_basename($model);
    	if (isset($model['id']) && $className != '') {
	        foreach (MediaFile::
	        	join('media_file_relations as rel', 'rel.file_id', '=', 'media_files.id')
        		->where('rel.post_id', '=', $model->id)
        		->where('rel.post_type', '=', $className)
      			->orderBy('id', 'desc')->get() as $img) {
	            
	            	$this->images[$img['id']] = $img;
	        }
	    }
    	return $this;
    }

    // Формируем вывод для галерей(Для админки)
    public function prepGaleryData(&$list)
    {
    	$res = [];

        foreach ($list as $key => $file) {
        	$item = ['orig' => $this->genFileLink($file)['orig']];
        	
        	if ($file['file_type'] == 'image') {
        		$item['thumb'] = $this->genFileLink($file, [128, 128, 'fit']);
        	} else {
        		$item['thumb'] = '/backend/images/file.png';
        	}
			
			foreach (['id', 'orig_name', 'file_type'] as $key) {
				$item[$key] = Helpers::getDataField($file, $key);
			}

			$res[] = $item;
        }
        return $res;
    }


    // app('UploadedFiles')->getByField($post, 'gallery')->size([128, 128, 'fit'])->htmlImg(['class' => 'thumb']);
    // Получаем массивы картинок из поля.
    // app('UploadedFiles')->get($post['images'])->files();


    // Получаем данные по id. Можно указать как массив так и единичный элемент
    // Результат будет таким же либо массив либо единичный элемент
    public function get($id, $first = false)
    {	    	
    	$this->reqImgSize = [];

    	$this->reqResultArray = ($first) ? false : true;

    	if (is_array($id)) { // Если массив
    		if (count($id) > 0) {
    			if ($first) $this->reqFiles	= [reset($id)];
    			else $this->reqFiles = $id;
    		}
    		else $this->reqFiles = [];
    	} else { // Иначе
    		// Если значение не установлено ничего не возвращаем
    		if ($id == '' || $id == false) $this->reqFiles = [];
    		else $this->reqFiles = [ $id ];
    	}

        return $this;
    }

    // Инитим данные из поля, для выборки массива
    public function getByField($post, $field, $first = false)
    {
    	$this->get( Helpers::getDataField($post, $field, []), $first );

        return $this;
    }


    // Устанавливает размер возвращаемой миниатюры. Только для картинок.
    public function size($size)
    {
    	$this->reqImgSize[] = $size;

    	return $this;
    }


    // Приватная функция создает из файла тэг img, если картинкой не является выводит пустую строку.
    private function _htmlImg(&$file, $attr = [])
    {
		if ($file['file_type'] != 'image' ) return '';

		$attrNew = $attr;

		if (!isset($attr['title'])) 
    		$attrNew['title'] = Helpers::getDataField($file, 'img_title');
    	if (!isset($attr['alt'])) 
    		$attrNew['alt'] =  Helpers::getDataField($file, 'img_alt');

		$title = Helpers::getDataField($file, 'img_title');
		$alt = Helpers::getDataField($file, 'img_alt');

    	$countImgSize = count($this->reqImgSize);
		if ($countImgSize > 0) {
			$srcset = '';
			foreach ($this->reqImgSize as $key => $size) {
				// Получаем урлы миниатюры и если нету генерим ее
				$thumb = $this->genFileLink($file, $size);
				
				// Генерим srcset если функция size была вызвана более одного раза
				if($countImgSize > 1) {
					// Далее получаем текстовый размер
					$strSize = Uploads::sizesToStr($size);
					// Тут нужно получить ширину для srcset, если нет миниатюры не добавляем srcset 
					if(isset($file['sizes'][$strSize])){
						$srcset .= $thumb." ".$file['sizes'][$strSize]['size'][0]."w, ";
					}
				}
				if($key == 0) $src = $thumb;
			}
		} else $src = $this->getOrigPath($file); // Получаем оригинал
		
		if ($srcset != '') $srcset = 'srcset="'.mb_substr($srcset, 0, -2).'"';

		return '<img src="'.$src.'" '.$srcset.' '.Helpers::getAttrs($attrNew).'>';
    }

    // Получаем готовый тэг html img, только для картинок, если вызван метод size будут сгенереный нужные размеры, если метод size вызван несколько раз, будет сгенерирован тег srcset. src будет первый вызваный size, если $link = true будет создана ссылка с атрибутами linkAttr
    public function htmlImg($link = false, $attr = [], $linkAttr = [])
    {

    	$this->loadByArray($this->reqFiles);
    	$this->getFiles();

    	$res = [];
		foreach ($this->reqFiles as $id) {
			if (!isset($this->images[$id]) || $this->images[$id]['file_type'] != 'image' ) continue;

			$res[] = ($link)
				? "<a href=\"" 
					. $this->getOrigPath($this->images[$id]) . "\" " 
					. Helpers::getAttrs($linkAttr) . ">" 
					. $this->_htmlImg($this->images[$id], $attr) . "</a>"
				: $this->_htmlImg($this->images[$id], $attr);
		}
// dd($this->reqResultArray);
		if ($this->reqResultArray) return $res; 
		elseif (count($res) > 0) return $res[0];
    }

    // Получить список урлов, если вызван метод size будут сгенерены нужные размеры(только для изображений). 
    // Если указано нескольколь размеров, то будет отдан массив с размерами по порядку указания.
    // Парметр $attr добавляет дополнительный опции из массива файла
    public function url($attr = [])
    {
    	$this->loadByArray($this->reqFiles);
    	$this->getFiles();

    	$res = [];

		foreach ($this->reqFiles as $id) {
			// Если файла нет игнорим
			if (!isset($this->images[$id])) continue;

    		$file = [ 'orig' => $this->getOrigPath($this->images[$id]) ];

			if (count($this->reqImgSize) > 0) {
				foreach ($this->reqImgSize as $size) {
					$file['sizes'][] = $this->genFileLink($this->images[$id], $size);
				}
			}

			if (count($attr) > 0) {
				foreach ($attr as $key) {
					$file['attr'][$key] = Helpers::getDataField($this->images[$id], $key, '');
				}
			}
			$res[] = $file;
		}

		if ($this->reqResultArray) return $res;
		return (count($res) > 0) ? $res[0] : [];
    }

    // Выведет нужный ключ или значение второго параметра defValue.
    public function urlOrEmpty($key, $defValue = '', $attr = [])
    {
    	$res = $this->url($attr);
    	
    	return Helpers::getDataField($res, $key, $defValue);
    }


    // Получить массив файлов
    public function files()
    {
    	$this->loadByArray($this->reqFiles);
    	$this->getFiles();

    	$res = [];

		foreach ($this->reqFiles as $id) {
			// Если файла нет игнорим
			if (!isset($this->images[$id])) continue;

			$res[] = &$this->images[$id];
		}

		if ($this->reqResultArray) return $res;
		elseif (count($res) > 0) return $res[0];

		return [];
    }

    // Удаляет  файл, если передан массив, удалит массив файлов
    public function deleteFiles($files)
    {
    	if (!is_array($files)) $files = [ $files ];

    	if (count($files) == 0) return;

    	Uploads::deleteFiles( MediaFile::whereIn('id', $files)->get() );
    }
}