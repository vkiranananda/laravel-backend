<?php
	// Default config file
	return [
	    // Опции списка
		'list' => [
			// Количество записей на странице
			'count-items' => 30,
			// Кнопка создания новой записи
			'create' => true,
			// Ссылки редактирования, удаления, клонирования и просмотра у каждой записи.
            // Если false исчезнет данная ссылка и кнопка что на нее ссылается.
			'item-edit' => true,
			'item-destroy' => true,
			'item-clone' => true,
            'item-view' => true,
			// Меню для записи в списке
			'item-menu-default' => [
				'edit' => [ 'label' => 'Править', 'link' => 'edit', 'icon' => 'pencil' ],
				'clone' => [ 'label' => 'Клонировать', 'link' => 'clone', 'icon' => 'file-symlink-file' ],
				'destroy' => [ 'label' => 'Удалить', 'link' => 'destroy', 'icon' => 'trashcan', 'confirm' => 'Вы действительно хотите удалить эту запись?' ]
			],
			'default-order' => ['col' => 'id', 'type' => 'desc'],
			// Сортировка списка перетаскиванием объектов, должны быть созданы соответсвующие роутнг и поле в бд sort_num
			'sortable' => false,
		],
        // Опции редактирования записи
		'edit' => [
		    // Шаблон
			'template' => 'Form::edit',
            // Кнопки создать и прочие внизу страницы
            'buttons-default' => [
                'save-and-exit' => [
                    'label' => 'Сохранить и выйти',
                    'hook' => 'FormSendAndExit'
                ],
                'save' => [
                    'label' => 'Сохранить',
                    'hook' => 'FormSend',
                    'type' => 'secondary'
                ],
            ]
		],
        // Опции просмотра
		'show' => [
		    // Шаблон
			'template' => 'Form::show',
            // Кнопки создать закрыть и редактировать внизу страницы
            'buttons-default' => [
                'exit' => [
                    'label' => 'Выйти',
                    'url' => 'javascript:history.back()',
                    'type' => 'secondary',
                ],
                'edit' => [
                    'label' => 'Редактировать',
                    'url' => ''
                ],
            ]
		],
        // Опции загрузки файлов
		'upload' => [
			// Контролер для работы с загрузкой файлов, лучше не менять, там много что на это завязано
			'controller' => 'UploadController',
            // Разрешнить загрузку файлов, должны быть созданы соответсвующие роутнги
			'enable' => false,
		],
        // Массив дополнительных параметров которые будут прикрепляться к url адресу в списке и создании новой записи.
		'url-params' => [],
	];
