//Инитим элементы HTML как ссылки
$('[data-role=href]').click(function(){
    window.location = $(this).attr('href');
});

//Обновляем страницу при переходе по хистори...
if(!!window.performance && window.performance.navigation.type === 2)
{
    console.log('Reloading');
    window.location.reload();
}

//Показываем скрываем элементы 
$('input[data-role = showHide]').click(function(){
    $($(this).attr('data-show')).show();
    $($(this).attr('data-hide')).hide();
}).each(function(){
    if($(this).attr('checked') !== undefined){
        $(this).trigger( "click" );
    }
});


//Удаление записей
$('[data-role=delete-record]').click(function(){
    var url = $(this).attr('href');
    if(confirm('Вы действительно хотите удалить эту запись?')){
        $.ajax({
            url:url,
            method: 'DELETE',
            data: {'_token':$('[name=_token]').val() }, 
            success: function(msg){
                location.reload();
                console.log(msg);
            },
            error: function(msg)
            {
                console.log(msg.responseText); // в консоле  отображаем информацию об ошибки, если они есть
            }
        });
    }
    return false;
});

$('#list-sortable-link').click(function(){
    Vue.prototype.$bus.$emit('TheListSortableShow', $(this).attr('href'));
    $('.dropdown-menu.show').dropdown('toggle');

    return false;
});



// File uploads
