<?php
	$conf = GetConfig::backend("Upload::uploads");
	$conf['file-name-type'] = 'id';
	return $conf;
?>