<?php
	$conf = BackendConfig::get("Upload::uploads");
	$conf['file-name-type'] = 'orig';
	return $conf;
?>