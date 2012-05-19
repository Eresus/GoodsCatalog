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
include_once TESTS_SRC_DIR . '/goodscatalog/classes/GoodsClientUI.php';
include_once TESTS_SRC_DIR . '/goodscatalog/classes/Good.php';

/**
 * @package GoodsCatalog
 * @subpackage Tests
 */
class GoodsCatalog_GoodsClientUI_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * http://bugs.eresus.ru/view.php?id=584
	 * @covers GoodsCatalogGoodsClientUI::renderList
	 */
	public function test_issue584()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}
		$template = $this->getMock('stdClass', array('compile'));
		$template->expects($this->any())->method('compile');

		$helper = $this->getMock('stdClass', array('prepareTmplData', 'getClientTemplate'));
		$helper->expects($this->any())->method('prepareTmplData')->
			will($this->returnValue(array()));
		$helper->expects($this->any())->method('getClientTemplate')->
			will($this->returnValue($template));

		$plugin = $this->getMock('ContentPlugin', array('getHelper'));
		$plugin->settings = array('goodsPerPage' => 1);
		$plugin->expects($this->any())->method('getHelper')->will($this->returnValue($helper));
		$test = new GoodsCatalogGoodsClientUI($plugin);

		$GLOBALS['page'] = $this->getMock('stdClass', array('httpError'));
		$GLOBALS['page']->topic = null;
		$GLOBALS['page']->subpage = 0;
		$GLOBALS['page']->id = 1;
		$GLOBALS['page']->expects($this->never())->method('httpError');

		$db = $this->getMock('stdClass', array('fetchAll', 'fetch'), array(), 'DB_Mock');
		$db->expects($this->once())->method('fetch')->will($this->returnValue(array('count' => 0)));
		DB::setMock($db);

		$test->getHTML();
	}
	//-----------------------------------------------------------------------------

	/* */
}
