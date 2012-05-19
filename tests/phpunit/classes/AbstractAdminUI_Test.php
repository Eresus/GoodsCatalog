<?php
/**
 * Каталог товаров
 *
 * Модульные тесты
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
 * @subpackage Tests
 *
 * $Id$
 */

include_once dirname(__FILE__) . '/../bootstrap.php';
include_once TESTS_SRC_DIR . '/goodscatalog/classes/AbstractUI.php';
include_once TESTS_SRC_DIR . '/goodscatalog/classes/AbstractAdminUI.php';

/**
 * @package GoodsCatalog
 * @subpackage Tests
 */
class GoodsCatalog_AbstractAdminUI_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * Проверяем extendedActions
	 * @covers GoodsCatalog_AbstractAdminUI::extendedActions
	 */
	public function test_extendedActions()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '<'))
		{
			$this->markTestSkipped('PHP 5.3.2 required');
		}
		$extendedActions = new ReflectionMethod('GoodsCatalog_AbstractAdminUI', 'extendedActions');
		$extendedActions->setAccessible(true);
		$plugin = new GoodsCatalog_Stub();
		$this->assertFalse($extendedActions->invoke(
			new GoodsCatalogAbstractAdminUITest_Stub($plugin)));
	}
	//-----------------------------------------------------------------------------

	/* */
}

class GoodsCatalogAbstractAdminUITest_Stub extends GoodsCatalog_AbstractAdminUI
{
	/**
	 * Метод должен возвращать имя класса активной записи
	 *
	 * @return string
	 */
	protected function getActiveRecordClass()
	{
		return null;
	}
	//-----------------------------------------------------------------------------

}