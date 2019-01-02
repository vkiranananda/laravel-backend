<?php
return [
    'store-type' => 0, //Видимость:  0 временные(удаляться через сутки), 1 видимость всем, 2 только для модуля.
    'file-name-type' => 'id', //Если id то имя файла береться из id, иначе береться оригинальное имя.
	'validate' => 'required|mimes:jpeg,jpg,gif,png',
	'disk' => 'uploads',	//Диск
	'url' => url('/uploads').'/', //Урл файла

	'sizes' => [
		'thumb' => [128, 128, 'fit'],
	]
];
?>