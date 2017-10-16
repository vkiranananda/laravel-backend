<?php
	$conf = BackendConfig::get("Upload::uploads");
	$conf['file-name-type'] = 'name';
	return $conf;
?>