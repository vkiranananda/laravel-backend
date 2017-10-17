<ul class="list-group menu-tree" id="menu-category-tree">
  <li class="list-group-item list-group-item-action title tree-item-title">
        <b>Структура</b>
        <div class="dropdown">
            <button class="btn btn-secondary button-grabber" type="button" id="dropdownMenuButton-0"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton-0">
              <a class="dropdown-item" href="{{ action('\Backend\Root\Category\Controllers\CategoryRootController@create')}}"><span class="icons-directory"></span>&nbsp;Новый раздел</a>
              <!-- <a class="dropdown-item" href="{{action('\Backend\Root\Category\Controllers\CartController@index')}}"> -->
              <!-- <span class="icons-trash"></span>&nbsp;Корзина</a> -->
            </div>
          </div>
  </li>
  @if(Categories::count() == 0)
  <li class="list-group-item list-group-item-action">
      <a href="{{ action('\Backend\Root\Category\Controllers\CategoryRootController@create') }}">
        <span class="icons-plus add-root"></span>&nbsp;Добавить раздел</a>
  </li>
  @endif
 {!! generateCategoryLists(Categories::getCat()) !!}

</ul>


<?php


function generateCategoryLists($elements, $parentId = 0, $depth = 0, $type = '') {  

	$ul = false;
	$res = '';

    $current = Request::input('cat', false);

    $catModuels = BackendConfig::get('category-modules');

    foreach ($elements as $el) {
        if ($el['parent_id'] == $parentId) {
        	$hidden = '';
        	$dataExpand = 'false';
          $active = ($current == $el['id']) ? 'active' : '' ;

          //Берем свойства type  у корневой записи
          if ( $parentId == 0 ) {
            $type = ( isset($el['conf']['type']) ) ? $el['conf']['type'] : '' ;
          }

          $next = generateCategoryLists($elements, $el['id'], $depth + 1, $type);
        	$liClass = ($depth == 0) ? '' :  'list-group-item-next';
        	$res .= "<li class='list-group-item $liClass' data-id='".$el['id']."'>";

        	$paddingTree = ($depth > 0) ? "style='padding-left: ".($depth*20)."px'" : '' ;
        	$res .= "<span class='list-group-item-action tree-item $active'>";

        	if($depth > 0)$res .= "<span style='padding-left: ".($depth*20)."px'></span>";

			    if($next != ''){
	        	if(Session::has('config.category.tree.'.$el['id']) ) {
	        		$dataExpand = 'true';
	        	}else {
	        		$hidden = 'style="display: none"';
	        	}
        		$res .= "<span class='octicon' data-expand='$dataExpand'></span>";
        	}
        	$res .= "<a href='".action(Categories::getModuleControllers($el['mod'])['resourceController'].'@index').'?cat='.$el['id']."'><span class='icons-directory'></span>&nbsp;".$el['name']."</a>";

          $controller = ($el['parent_id'] == 0) ? 'Root' : '' ;
        	$res .= '<div class="dropdown">
              <button class="btn btn-secondary button-grabber" type="button" id="dropdownMenuButton-'.$el['id'].'"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton-'.$el['id'].'">';

          if( $type == 'hierarchical' ){
            $res .= '<a class="dropdown-item" href="'.action('\Backend\Root\Category\Controllers\CategoryController@create').'?cat='.$el['id'].'"><span class="icons-directory"></span>&nbsp;Новая категория</a>';
          }

          $res .= '<a class="dropdown-item" href="'.action('\Backend\Root\Category\Controllers\Category'.$controller.'Controller@edit', $el['id']).'"><span class="icons-pencil"></span>&nbsp;Править</a>
                <a class="dropdown-item" href="'.action('\Backend\Root\Category\Controllers\CategoryController@destroy', $el['id']).'" data-role="delete-record"><span class="icons-trash"></span>&nbsp;Удалить</a>
              </div>
            </div>';


        	$res .= "</span>";
        	if($next != '') {
        		$res .= "<ul class='list-group' $hidden>$next</ul>";
        	}
        	$res .= "</li>\n";
        }
    }
    return $res;
}
?>