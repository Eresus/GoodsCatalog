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
	 * @covers GoodsCatalog_GoodsClientUI::renderList
	 */
	public function test_issue584()
	{
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
		$test = new GoodsCatalog_GoodsClientUI($plugin);

		$page = $this->getMock('stdClass', array('httpError'));
		$page->topic = null;
		$page->subpage = 0;
		$page->id = 1;
		$page->expects($this->never())->method('httpError');

		$app = $this->getMock('stdClass', array('getPage'));
		$app->expects($this->any())->method('getPage')->will($this->returnValue($page));

		$mock = $this->getMock('stdClass', array('app', 'fetchAll', 'fetch'));
		$mock->expects($this->any())->method('app')->will($this->returnValue($app));
		$mock->expects($this->once())->method('fetch')->will($this->returnValue(array('count' => 0)));
		DB::setMock($mock);

		$test->getHTML();
	}

	/**
	 * В КИ "подробно" отображается товар, даже если он неактивен
	 * @link http://bugs.eresus.ru/view.php?id=781
	 * @covers GoodsCatalog_GoodsClientUI::renderItem
	 * @expectedException DomainException
	 */
	public function test_issue781()
	{
		$plugin = $this->getMock('ContentPlugin');
		$test = new GoodsCatalog_GoodsClientUI($plugin);

		$page = $this->getMock('stdClass', array('httpError'));
		$page->expects($this->once())->method('httpError')->with(404)->
			will($this->throwException(new DomainException()));
		$page->topic = 1;

		$app = $this->getMock('stdClass', array('getPage'));
		$app->expects($this->any())->method('getPage')->will($this->returnValue($page));

		$good = array('active' => false);

		$mock = $this->getMock('stdClass', array('fetch', 'app'), array());
		$mock->expects($this->any())->method('app')->will($this->returnValue($app));
		$mock->expects($this->once())->method('fetch')->will($this->returnValue($good));
		DB::setMock($mock);

		$m_renderIrem = new ReflectionMethod('GoodsCatalog_GoodsClientUI', 'renderItem');
		$m_renderIrem->setAccessible(true);
		$m_renderIrem->invoke($test);
	}
	//-----------------------------------------------------------------------------

	/* */
}
