<?php
	return [
		'lang' => [
			'list-title' => 'Список "ключ-значение"',
			'create-title' => 'Создать "ключ-значение"',
			'edit-title' => 'Редактирование "ключ-значение"',
		],
		'uploads' => true,
		'list' => [
			'order' => [
				[ 'label' => 'Имя по возрастанию', 'column' => 'name', 'type' => 'asc' ],
				[ 'label' => 'Имя по убыванию', 'column' => 'name', 'type' => 'desc' ],
				[ 'label' => 'Типу', 'column' => 'type', 'type' => 'asc' ],
				[ 'label' => 'Автозагрука', 'column' => 'autoload', 'type' => 'asc' ],
			]		
		]

	];