<?php

	Backend::installResourceRoute('cat', 'Category');
	Backend::installSortableRoute('sortable', 'Category');

	Backend::installResourceRoute('cat-root', 'Category', 'CategoryRoot');
	Backend::installSortableRoute('sortable-root', 'Category', 'CategoryRoot');
	
	Route::get('routes', '\Backend\Category\Controllers\InfoController@routes');
