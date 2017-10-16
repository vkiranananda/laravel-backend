$('#menu-category-tree [data-expand]').bind('click', function(){
    var treeLi = $(this).closest('li');
    var treeUl = treeLi.find("ul").first();

    if($(this).attr('data-expand') == 'true') {
        $(this).attr('data-expand', 'false');
        saveTreeExpand(treeLi.attr('data-id'), '0');
        treeUl.hide(500);
    }else {
        $(this).attr('data-expand', 'true');
        saveTreeExpand(treeLi.attr('data-id'),'1');
        treeUl.show(500);
    }
    function saveTreeExpand($id, $status){
        $.ajax({
          type: "GET",
          url: "/content/cat/tree-expand/"+$id+"/"+$status,
        });
    }
});
