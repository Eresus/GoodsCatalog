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

include_once dirname(__FILE__) . '/helpers.php';
include_once dirname(__FILE__) . '/../../src/goodscatalog/classes/AbstractUI.php';
include_once dirname(__FILE__) . '/../../src/goodscatalog/classes/AbstractAdminUI.php';
include_once dirname(__FILE__) . '/../../src/goodscatalog/classes/GoodsAdminUI.php';

/**
 * @package GoodsCatalog
 * @subpackage Tests
 */
class GoodsCatalogGoodsAdminUITest extends PHPUnit_Framework_TestCase
{
	private $fixture;

	/**
	 * Setup test enviroment
	 */
	protected function setUp()
	{
		// @codeCoverageIgnoreStart
		$this->fixture = new GoodsCatalogGoodsAdminUI(new GoodsCatalog_Stub());
		// @codeCoverageIgnoreEnd
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем метод getDbTable
	 * @covers GoodsCatalogAbstractActiveRecord::getDbTable
	 */
	public function testGetDbTable()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '<'))
		{
			$this->markTestSkipped('PHP 5.3.2 required');
		}

		$GLOBALS['Eresus'] = new stdClass();
		$GLOBALS['Eresus']->sections = new GoodsCatalogGoodsAdminUITest_SectionsStub();

		$buildSectionTree = new ReflectionMethod('GoodsCatalogGoodsAdminUI', 'buildSectionTree');
		$buildSectionTree->setAccessible(true);

		$expected = array(
			array('id' => 1, 'type' => 'default', 'padding' => '', 'selectable' => false),
			array('id' => 11, 'type' => 'goodscatalog', 'padding' => '-', 'selectable' => true),
			array('id' => 12, 'type' => 'goodscatalog', 'padding' => '-', 'selectable' => true),
			array('id' => 2, 'type' => 'goodscatalog', 'padding' => '', 'selectable' => true),
			);

		$this->assertEquals($expected, $buildSectionTree->invoke($this->fixture, 0));
	}
	//-----------------------------------------------------------------------------

	/* */
}

/*******************************************************************************
 *
 * ЗАГЛУШКИ
 *
 *******************************************************************************/

class GoodsCatalogGoodsAdminUITest_SectionsStub
{
	public function children($id)
	{
		switch ($id)
		{
			case 0:
				return array(
					array('id' => 1, 'type' => 'default'),
					array('id' => 2, 'type' => 'goodscatalog')
				);
			break;
			case 1:
				return array(
					array('id' => 11, 'type' => 'goodscatalog'),
					array('id' => 12, 'type' => 'goodscatalog')
				);
			break;
		}
		return array();
	}
	//-----------------------------------------------------------------------------
}