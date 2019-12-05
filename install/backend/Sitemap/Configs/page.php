<?php
	return [
		'enable' => [
	        'type' => 'select', 
	        'name' => 'enable', 
	        'label' => 'Активна',
	        'desc' => 'Запись будет показана на карте сайта',
	        'value' => '1',
	        'options' => [
	        	['value' => '1', 'label' => 'Да'],
	        	['value' => '0', 'label' => 'Нет'],
	        ]
	    ],
	    'freq' => [
	        'name' => 'freq', 
	        'type' => 'select', 
	        'label' => 'Частота обновления записей',
	        'field-save' => 'array',
	        'value' => '',
	        'desc' => 'Если не выбрано, то наследуется от родительской категории, если не выбрано и там, остается пустым',
	        'options' => [
	        	['value' => '', 'label' => ''],
	        	['value' => 'always', 'label' => 'Постоянно'],
	        	['value' => 'hourly', 'label' => 'Каждый час'],
	        	['value' => 'daily', 'label' => 'Каждый день'],
	        	['value' => 'weekly', 'label' => 'Каждую неделю'],
	        	['value' => 'monthly', 'label' => 'Каждый месяц'],
	        	['value' => 'yearly', 'label' => 'Каждый код'],
	        	['value' => 'never', 'label' => 'Никогда'], 
	        ],
	    ],
	    'priority' => [
	        'name' => 'priority', 
	        'type' => 'select', 
	        'label' => 'Приоритет',
	        'field-save' => 'array',
	        'value' => '',
	        'desc' => 'Если не выбрано, то наследуется от родительской категории, если не выбрано и там, остается пустым',
	        'options' => [
	        	['value' => '', 'label' => ''],
	        	['value' => '0.1', 'label' => '0.1'],
	        	['value' => '0.2', 'label' => '0.2'],
	        	['value' => '0.3', 'label' => '0.3'],
	        	['value' => '0.4', 'label' => '0.4'],
	        	['value' => '0.5', 'label' => '0.5'],
	        	['value' => '0.6', 'label' => '0.6'],
	        	['value' => '0.7', 'label' => '0.7'],
	        	['value' => '0.8', 'label' => '0.8'],
	        	['value' => '0.9', 'label' => '0.9'],
	        	['value' => '1.0', 'label' => '1.0'],	            		            		
	        ],
	    ],
	];