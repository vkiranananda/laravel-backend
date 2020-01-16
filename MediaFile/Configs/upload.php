<?php
return [
    'file-name-type' => 'id', //Если id то имя файла береться из id, иначе береться оригинальное имя.
	'validate' => 'required|mimes:jpeg,jpg,gif,png',
	'disk' => 'uploads',	//Диск
	'url' => '/uploads/', //Урл файла

	'sizes' => [
		'thumb' => [128, 128, 'fit'],
	]
];
?>