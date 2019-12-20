<?php
namespace Backend\Root\MediaFile\Services;
use \Backend\Root\MediaFile\Models\MediaFile;
use \Backend\Root\MediaFile\Models\MediaFileRelation;
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
    public function genFileLink($file, $size)
    {  
        if (count($size) > 0 && $file['file_type'] == 'image') {
			if (pathinfo($file['file'])['extension'] == 'gif') {
            	return $this->getOrigUrl($file);
            } else {
            	$sizeStr = Uploads::sizesToStr($size);
	          
	          	// Если миниатюрки нет.
	            if (! isset($file['sizes'][$sizeStr])) {
	                if (!is_array($file['sizes'])) $file['sizes'] = [];
	                
	                $file['sizes'] = array_merge($file['sizes'], Uploads::genSizes($file, [ $size ]));
	                $file->save();
	            }
	            return $file['url'].$file['path'].$file['sizes'][$sizeStr]['path'].urlencode($file['sizes'][$sizeStr]['file']);
	        }
        } 
        return false;
    }

    public function getOrigUrl($file) {
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
    public function loadByArray($array) 
    {
    	if (is_array($array)) {
    		foreach ($array as $id) {
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
    public function loadByPost($post) 
    {
    	$className = class_basename($post);
    	if (isset($post['id']) && $className != '') {
	        foreach (MediaFile::
	        	join('media_file_relations as rel', 'rel.file_id', '=', 'media_files.id')
        		->where('rel.post_id', '=', $post->id)
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
        	$item = ['orig' => $this->getOrigUrl($file)];
        	
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
    	$srcset = '';
		if ($countImgSize > 0) {
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
		} else $src = $this->getOrigUrl($file); // Получаем оригинал
		
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
					. $this->getOrigUrl($this->images[$id]) . "\" " 
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

    		$file = [ 'orig' => $this->getOrigUrl($this->images[$id]) ];

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



    // Просто удаляем файл. Если связи есть, файл не трогается. Нужно удалить сначала связи.
    // Если передан массив, удалит массив файлов.
    private function deleteFiles($files)
    {
    	if (!is_array($files)) $files = [ $files ];

    	if (count($files) == 0) return;

    	// Меням ключи со значениями местами
    	$delFiles = array_flip($files);

    	// Смотрим, если для какой то записи есть связь, не удаляем файл, убирая его из массива.
    	foreach (MediaFileRelation::whereIn('file_id', $files)->get(['file_id']) as $rel) {
    		if (isset($delFiles[$rel['file_id']])) unset($delFiles[$rel['file_id']]);
    	}

    	// Закидываем ключи обратно в массив
    	$delFiles = array_keys($delFiles);
		
		// Если файлов на удаление больше 0, то удаляем.
		if (count($delFiles) > 0) Uploads::deleteFiles(MediaFile::whereIn('id', $delFiles)->get());
    }


    // Удаляет  файл, с переданными связями postType и postId, 
    // если передан массив, удалит массив файлов. 
    // если останутся еще какие то связи то файл не удалится.
    // Если передан четвертый параметр как true. То удалятся только связи, а файл остенется.
    public function deleteFilesByRelation($files, $postType, $postId, $soft = false)
    {
    	
    	if (!is_array($files)) $files = [ $files ];

    	if (count($files) == 0) return;

    	// Удаляем связи
    	$relations = MediaFileRelation::
    		whereIn('file_id', $files)
    		->where('post_type', $postType)
    		->where('post_id', $postId)
    		->delete();

    	// Если удаление мягкое, то файлы не трогаем.
    	if ($soft) return;

    	// Иначе пытаемся грохнуть файлы физически
    	$this->deleteFiles($files);
    }
}