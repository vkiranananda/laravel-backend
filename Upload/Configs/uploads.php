<?php
return [
    'store-type' => 0, //Видимость только для данного модуля.  0 временные, удаляться через сутки, 1 видимость всем, 2 только для модуля.
    'file-name-type' => 'id', //Если id то имя файла береться из id, иначе береться оригинальное имя.
	'validate' => 'required|mimes:jpeg,jpg,gif,png,JPEG,GIF,PNG',
	'disk' => 'uploads',	//Диск
	'url' => url('/uploads').'/', //Урл файла

	'sizes' => [
		'thumb' => [128, 128, 'fit'],
	],
	//'save-info-url' => action('\Backend\Root\Upload\Controllers\EditController@saveInfo'),
];
?>