<?php
	$conf = GetConfig::backend("Upload::uploads");
	$conf['file-name-type'] = '';
	$conf['validate'] = 'required';
	$conf['store-type'] = 1;
	return $conf;
?>