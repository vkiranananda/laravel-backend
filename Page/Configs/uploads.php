<?php
	$conf = GetConfig::backend("Upload::uploads");
	$conf['file-name-type'] = 'orig';
	return $conf;
?>