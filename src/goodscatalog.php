<?php
/**
 * Каталог товаров
 *
 * Модуль позволяет создать на сайте простой каталог товаров
 *
 * @version ${product.version}
 *
 * @copyright 2010, ООО "Два слона", http://dvaslona.ru/
 * @license http://www.gnu.org/licenses/gpl.txt	GPL License 3
 * @author Сергей Каспари <ghost@dvaslona.ru>
 * @author Timofey Finogenov
 *
 * Данная программа является свободным программным обеспечением. Вы
 * вправе распространять ее и/или модифицировать в соответствии с
 * условиями версии 3 либо (по вашему выбору) с условиями более поздней
 * версии Стандартной Общественной Лицензии GNU, опубликованной Free
 * Software Foundation.
 *
 * Мы распространяем эту программу в надежде на то, что она будет вам
 * полезной, однако НЕ ПРЕДОСТАВЛЯЕМ НА НЕЕ НИКАКИХ ГАРАНТИЙ, в том
 * числе ГАРАНТИИ ТОВАРНОГО СОСТОЯНИЯ ПРИ ПРОДАЖЕ и ПРИГОДНОСТИ ДЛЯ
 * ИСПОЛЬЗОВАНИЯ В КОНКРЕТНЫХ ЦЕЛЯХ. Для получения более подробной
 * информации ознакомьтесь со Стандартной Общественной Лицензией GNU.
 *
 * Вы должны были получить копию Стандартной Общественной Лицензии
 * GNU с этой программой. Если Вы ее не получили, смотрите документ на
 * <http://www.gnu.org/licenses/>
 *
 * @package GoodsCatalog
 *
 * $Id$
 */

/**
 * Основной класс плагина
 *
 * @package GoodsCatalog
 */
class GoodsCatalog extends ContentPlugin
{
	/**
	 * Версия плагина
	 * @var string
	 */
	public $version = '${product.version}';

	/**
	 * Требуемая версия ядра
	 * @var string
	 */
	public $kernel = '2.12';

	/**
	 * Название плагина
	 * @var string
	 */
	public $title = 'Каталог товаров';

	/**
	 * Опиание плагина
	 * @var string
	 */
	public $description = 'Простой каталог товаров';

	/**
	 * Тип плагина
	 * @var string
	 */
	public $type = 'client,admin,content';

	/**
	 * Действия при инсталляции
	 *
	 * @return void
	 * @see main/core/Plugin::install()
	 */
	public function install()
	{
		parent::install();

		/*
		 * Таблица товаров
		 */
		$sql = "
			`id` int(10) unsigned NOT NULL auto_increment COMMENT 'Идентификатор',
			`section` int(10) unsigned NOT NULL COMMENT 'Привязка к разделу',
			`active` bool NOT NULL default 0 COMMENT 'Активность',
			`position` int(10) unsigned NOT NULL default '0' COMMENT 'Порядковый номер',
			`article` varchar(255) NOT NULL default '' COMMENT 'Артикул',
			`title` varchar(255) NOT NULL default '' COMMENT 'Название',
			`about` text NOT NULL default '' COMMENT 'Краткое описание',
			`description` longtext NOT NULL default '' COMMENT 'Описание',
			`ext` varchar(4) NOT NULL default '' COMMENT 'Расширение файла основной фотографии',
			`special` bool NOT NULL default 0 COMMENT 'Спецпредложение',
			`brand` int(10) unsigned default NULL COMMENT 'Привязка к бренду',
			PRIMARY KEY  (`id`),
			KEY `admin_list` (`section`, `position`),
			KEY `client_list` (`active`, `section`, `position`),
			KEY `admin_special` (`special`),
			KEY `client_special` (`active`, `special`)
		";
		$this->dbCreateTable($sql, 'goods');

		/*
		 * Таблица брендов
		 */
		$sql = "
			`id` int(10) unsigned NOT NULL auto_increment COMMENT 'Идентификатор',
			`active` bool NOT NULL default 0 COMMENT 'Активность',
			`title` varchar(255) NOT NULL default '' COMMENT 'Название',
			`description` longtext NOT NULL default '' COMMENT 'Описание',
			`ext` varchar(4) NOT NULL default '' COMMENT 'Расширение файла логотипа',
			PRIMARY KEY  (`id`),
			KEY `admin_list` (`title`),
			KEY `client_list` (`active`, `title`)
		";
		$this->dbCreateTable($sql, 'brands');

		/*
		 * Таблица дополнительных фотографий
		 */
		$sql = "
			`id` int(10) unsigned NOT NULL auto_increment COMMENT 'Идентификатор',
			`active` bool NOT NULL default 0 COMMENT 'Активность',
			`position` int(10) unsigned NOT NULL default '0' COMMENT 'Порядковый номер',
			`goods` int(10) unsigned default 0 COMMENT 'Привязка к товару',
			`ext` varchar(4) NOT NULL default '' COMMENT 'Расширение файла',
			PRIMARY KEY  (`id`),
			KEY `admin_list` (`goods`, `position`),
			KEY `client_list` (`active`, `goods`, `position`)
		";
		$this->dbCreateTable($sql, 'photos');

	}
	//-----------------------------------------------------------------------------
}
