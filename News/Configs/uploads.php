<?php
	$conf = BackendConfig::get("Post::uploads");
	$conf['validate'] = 'required';
	return $conf;
?>