<?php
/**
 * Каталог товаров
 *
 * Абстрактный пользоательский интерфейс
 *
 * @version ${product.version}
 *
 * @copyright 2010, ООО "Два слона", http://dvaslona.ru/
 * @license http://www.gnu.org/licenses/gpl.txt	GPL License 3
 * @author Михаил Красильников <mk@3wstyle.ru>
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
 * Абстрактный пользоательский интерфейс
 *
 * @package GoodsCatalog
 */
abstract class GoodsCatalogAbstractUI
{
	/**
	 * Объект плагина
	 *
	 * @var GoodsCatalog
	 */
	protected $plugin;

	/**
	 * Имя класса ActiveRecord, соответствующего типу объектов с которыми работает интерфейс
	 *
	 * @var string
	 * @see getActiveRecordClass
	 */
	protected $activeRecordClass;

	/**
	 * Конструктор
	 *
	 * @param GoodsCatalog $plugin экземпляр класса плагина GoodsCatalog
	 *
	 * @uses getActiveRecordClass
	 * @since 1.00
	 */
	public function __construct(GoodsCatalog $plugin)
	{
		$this->plugin = $plugin;
		$this->activeRecordClass = $this->getActiveRecordClass();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Должен возвращать HTML-разметку интерфейса
	 *
	 * @return string
	 */
	abstract public function getHTML();
	//-----------------------------------------------------------------------------

	/**
	 * Метод должен возвращать имя класса активной записи соответствующего типу объектов,
	 * с которыми работет интерфейс
	 *
	 * @return string
	 */
	abstract protected function getActiveRecordClass();
	//-----------------------------------------------------------------------------

}