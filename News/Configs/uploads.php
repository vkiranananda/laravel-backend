<?php
	$conf = GetConfig::backend("Post::uploads");
	$conf['validate'] = 'required';
	return $conf;
?>