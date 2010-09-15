<?php
/**
 * Каталог товаров
 *
 * Абстрактный интерфейс
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
 * Абстрактный интерфейс
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
	 * Класс AR
	 *
	 * @var string
	 */
	private $activeRecordClass;

	/**
	 * Конструктор
	 *
	 * @param GoodsCatalog $plugin
	 *
	 * @return GoodsCatalogBrandsAI
	 */
	public function __construct(GoodsCatalog $plugin)
	{
		$this->plugin = $plugin;
		$this->activeRecordClass = $this->getActiveRecordClass();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Должен возвращать HTML интерфейса
	 *
	 * @return string
	 */
	abstract public function getHTML();
	//-----------------------------------------------------------------------------

	/**
	 * Метод должен возвращать имя класса активной записи
	 *
	 * @return string
	 */
	abstract protected function getActiveRecordClass();
	//-----------------------------------------------------------------------------

}