<?php
	$conf = GetConfig::backend("Upload::uploads");
	$conf['file-name-type'] = 'name';
	return $conf;
?>