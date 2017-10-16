<?php
	$conf = BackendConfig::get("Upload::uploads");
	$conf['file-name-type'] = 'id';
	return $conf;
?>