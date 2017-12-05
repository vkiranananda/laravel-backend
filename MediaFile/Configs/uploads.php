<?php
	$conf = GetConfig::backend("Upload::uploads");
	$conf['file-name-type'] = '';
	$conf['validate'] = 'required|mimes:jpeg,gif,png';
	$conf['store-type'] = 1;
	return $conf;
?>