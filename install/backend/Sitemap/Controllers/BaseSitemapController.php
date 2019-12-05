<?php

namespace Backend\Sitemap\Controllers;

class BaseSitemapController 
{
	
	private $cats = [];
	private $urls = [];
	private $freq = '';
	private $priority = '';

	// Тут пишем код
	public function make()
	{

	}

	// Добавить урл
	public function addUrl($url, $date = '', $freq = '', $priority = '')
	{
		$this->urls[] = [
			'url' => $url,
			'freq' => ($freq == '') ? $this->freq : $freq,
			'priority' => ($priority == '') ? $this->priority : $priority,
			'date' => ($date != '') ? date("c", strtotime($date)) : ''
		];
	}

	// Добавить урл по категории
	public function addUrlbyCat($url, $catId, $date, $freq = '', $priority = '')
	{
		if (!isset($this->cats[$catId])) return false;

		$freq = ($freq == '') ? $this->cats[$catId]['freq'] : $freq;
		$priority = ($priority == '') ? $this->cats[$catId]['priority'] : $priority;

		$this->addUrl($url, $date, $freq, $priority);
	}	

	// Устанавливаем значения пол умолчанию
	public function setDefaultOpt($freq = '', $priority = '')
	{
		$this->freq = $freq;
		$this->priority = $priority;

		return $this;
	}

	// Получить категорию по id 
	public function getCat($id)
	{
		return (isset($this->cats[$id])) ? $this->cats[$id] : false;
	}

	// Получить текущие урлы
	public function getUrls()
	{
		return $this->urls;
	}

	// Установка списка категорий
	public function setCats($cats)
	{
		$this->cats = $cats;

		return $this;
	}

	// Получение массива айди категорий.
	public function getCatsId()
	{
		$res = [];
		
		foreach ($this->cats as $cat) {
			$res[] = $cat['id'];
		}

		return $res;
	}
}