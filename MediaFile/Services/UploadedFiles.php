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

    // Геренрим линки на файл
    public function genFileLink($file, $sizes = [])
    {  
        $size = Uploads::sizesToStr($sizes);

        $res['orig'] = $this->getOrigPath($file);

        if ($file['file_type'] == 'image') {
			if (pathinfo($file['file'])['extension'] == 'gif' || $size == '') {
            	$res['thumb'] = $res['orig'];
            } else {
	            if (! isset($file['sizes'][$size])) {
	                if (!is_array($file['sizes'])) $file['sizes'] = [];
	                
	                $file['sizes'] = array_merge($file['sizes'], Uploads::genSizes($file, [ $sizes ]));
	                
	                $file->save();
	            }

	            $res['thumb'] = $file['url'].$file['path'].$file['sizes'][$size]['path'].urlencode($file['sizes'][$size]['file']);
	        }
        } 
        return $res;
    }

    private function getOrigPath($file) {
    	return $file['url'].$file['path'].urlencode($file['file'] );
    }

    // Получаем ранее иниченные файл и Сохраняем в массив картинок
    public function getFiles()
    {
    	//Формируем новый массив на выборку, если файла нет в общем массиве
    	$idsReq = [];
    	foreach ($this->loadFiles as $id) {
    		if( ! isset($this->images[$id]) ) $idsReq[] = $id;
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
            if( ($gal = Helpers::dataIsSetValue($post, $field)) ){
                if (is_array($gal)) {
                	// Только первые картинки
                	if ($first) $req[] = reset($gal);
                	else $req = array_merge($req, $gal); 
                }
            }
        }

        $this->loadByArray($req);
    }

    // Добавляет данные для автоматической загрузки всех изображений, что бы не плодить запросы
    // указвается массив данных
    public function loadByArray($data) 
    {
    	if (is_array($data)) {
    		$this->loadFiles = array_merge($this->loadFiles, $data);
    	}

    	return $this;
    }

    // Добавляет данные для автоматической загрузки всех изображений, что бы не плодить запросы
    // Можно указать как название поля, так и массив из названий поля
    public function loadByField($post, $field) 
    {
    	$fields = (!is_array($field)) ? [$field] : $field;

    	foreach ($fields as $field) {
    		$this->loadByArray(Helpers::dataIsSetValue($post, $field)); 
    	}

    	return $this;
    }

    // Формируем вывод для галерей
    public function prepGaleryData(&$list)
    {
    	$res = [];
        foreach ($list as $key => $file) {
			$res[] = array_merge(
				( ($file['file_type'] == 'image') 
					? $this->genFileLink($file, [128, 128, 'fit']) 
					: ['orig' => $this->genFileLink($file)['orig'], 'thumb' => '/backend/images/file.png' ]),
				Helpers::setArray($file, ['id', 'orig_name', 'file_type'])
			);
        }
        return $res;
    }


    // app('UploadedFiles')->getByField($post, 'gallery')->size([128, 128, 'fit'])->htmlImg(['class' => 'thumb']);
    // Получаем массивы картинок из поля.
    // app('UploadedFiles')->get($post['images'])->files();

    // Получаем данные по id. Можкно указать как массив так и единичный элемент
    // Результат будет таким же либо массив либо единичный элемент
    // Если указан $first как истина, будет выведен только первый элемент
    // Запрос в бд будет 1
    public function get($id, $first = false)
    {	
    	$this->reqImgSize = [];
    	// Вернет массив
    	$this->reqResultArray = ($first) ? false : true; 

    	if (is_array($id)) { // Если массив
    		if (count($id) > 0) {
    			if ($first) $this->reqFiles = [ reset($id) ];
    			else $this->reqFiles = $id;
    		}
    		else $this->reqFiles = [];
    	} else { // Иначе
    		//Если значение не установлено ничего не возвращаем
    		if ($id == '' || $id == false) {
    			$this->reqFiles = [];
    		} else {
	    		// Вернет значение в переменной
	    		$this->reqFiles = [ $id ];
	    		$this->reqResultArray = false;
	    	}
    	}

        return $this;
    }

    // Инитим данные из поля, для выборки массива
    public function getByField($post, $field, $first = false)
    {
    	$this->get( Helpers::getDataField($post, $field), $first );

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
    		$attrNew['title'] = Helpers::dataIsSetValue($file, 'img_title');
    	if (!isset($attr['alt'])) 
    		$attrNew['alt'] =  Helpers::dataIsSetValue($file, 'img_alt');

		$title = Helpers::getDataField($file, 'img_title');
		$alt = Helpers::getDataField($file, 'img_alt');

    	$countImgSize = count($this->reqImgSize);
		if ($countImgSize > 0) {
			$srcset = '';
			foreach ($this->reqImgSize as $key => $size) {
				// Получаем урлы миниатюры и если нету генерим ее
				$thumb = $this->genFileLink($file, $size)['thumb'];
				
				// Генерим srcset если функция size была вызвана более одного раза
				if($countImgSize > 1) {
					// Далее получаем текстовый размер
					$strSize = Uploads::sizesToStr($size);
					// Тут нужно получить ширину для srcset, если нет миниатюры не добавляем srcset 
					if(isset($file['sizes'][$strSize])){
						$srcset .= $thumb." ".$file['sizes'][$strSize]['size'][0]."w, ";
					}
				}
				if($key == 0)$src = $thumb;
			}
		} else {
			$src = $this->genFileLink($file)['thumb'];
		}
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

		if ($this->reqResultArray) return $res; 
		elseif (count($res) > 0) return $res[0];
    }

    // Получить список урлов, если вызван метод size будут сгенерены нужные размеры(только для изображений). 
    // Если указано нескольколь размеров, то будет отдан массив с размерами по порядку указания.
    // Парметр $keys добавляет дополнительный опции из массива файла
    public function url($keys = [])
    {
    	$this->loadByArray($this->reqFiles);
    	$this->getFiles();

    	$res = [];

		foreach ($this->reqFiles as $id) {
			// Если файла нет игнорим
			if (!isset($this->images[$id])) continue;

			$file = $this->images[$id];
    		$data = [];

			if (count($this->reqImgSize) > 0) {
				$sizes = [];
				foreach ($this->reqImgSize as $size) {
					$sizes[] = $this->genFileLink($file, $size);
				}
				$data = (count($sizes) > 1) ? $sizes : $sizes[0];
			} else {
				$data = $this->genFileLink($file);
			}
			foreach ($keys as $key) {
				if (isset($file[$key])) $data[$key] = $file[$key];
			}
			$res[] = $data;
		}

		if ($this->reqResultArray) return $res;
		elseif (count($res) > 0) return $res[0];

		return [];
    }


    // Получить массив файлов
    public function files()
    {
    	$this->loadByArray($this->reqFiles);
    	$this->getFiles();

    	$res = [];

		foreach ($this->reqFiles as $id) {
			// Если файла нет игнорим
			if(!isset($this->images[$id])) continue;

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