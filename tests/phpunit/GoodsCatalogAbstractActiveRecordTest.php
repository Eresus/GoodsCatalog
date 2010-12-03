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
include_once dirname(__FILE__) . '/../../src/goodscatalog/classes/AbstractActiveRecord.php';

/**
 * @package GoodsCatalog
 * @subpackage Tests
 */
class GoodsCatalogAbstractActiveRecordTest extends PHPUnit_Framework_TestCase
{
	private $fixture;

	/**
	 * Setup test enviroment
	 */
	protected function setUp()
	{
		// @codeCoverageIgnoreStart
		$this->fixture = new GoodsCatalogAbstractActiveRecordTest_Stub();
		// @codeCoverageIgnoreEnd
	}
	//-----------------------------------------------------------------------------

	/**
	 * Clean up test enviroment
	 * @see Framework/PHPUnit_Framework_TestCase::tearDown()
	 */
	protected function tearDown()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '>='))
		{
			$plugin = new ReflectionMethod('GoodsCatalogAbstractActiveRecord', 'plugin');
			$plugin->setAccessible(true);
			$plugin->invoke(null, new GoodsCatalog_Stub());
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем GoodsCatalogAbstractActiveRecord::plugin
	 *
	 */
	public function test_plugin()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '<'))
		{
			$this->markTestSkipped('PHP 5.3.2 required');
		}

		$plugin = new ReflectionMethod('GoodsCatalogAbstractActiveRecord', 'plugin');
		$plugin->setAccessible(true);

		$GLOBALS['Eresus'] = new stdClass();
		$GLOBALS['Eresus']->plugins = new PluginsStub();

		$this->assertSame($GLOBALS['Eresus']->plugins->plugin, $plugin->invoke(null), 'Pass 1.1');
		$this->assertSame($GLOBALS['Eresus']->plugins->plugin, $plugin->invoke(null), 'Pass 1.2');

		$stub = new stdClass();
		$this->assertSame($stub, $plugin->invoke(null, $stub), 'Pass 2.1');
		$this->assertSame($stub, $plugin->invoke(null), 'Pass 2.2');

	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем конструктор
	 * @covers GoodsCatalogAbstractActiveRecord::__construct
	 */
	public function test_construct_wo_params()
	{
		$mock = $this->getMock('GoodsCatalogAbstractActiveRecordTest_Stub', array('loadById'), array(), '', false);
		$mock->expects($this->never())->method('loadById');
		$mock->__construct();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем конструктор
	 * @covers GoodsCatalogAbstractActiveRecord::__construct
	 */
	public function test_construct_with_params()
	{
		$mock = $this->getMock('GoodsCatalogAbstractActiveRecordTest_Stub', array('loadById'), array(), '', false);
		$mock->expects($this->once())->method('loadById');
		$mock->__construct(123);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем метод getDbTable
	 * @covers GoodsCatalogAbstractActiveRecord::getDbTable
	 */
	public function testGetDbTable()
	{
		$GLOBALS['Eresus'] = new stdClass();
		$GLOBALS['Eresus']->plugins = new PluginsStub();

		$this->assertEquals('goodscatalog_mytable', $this->fixture->getDbTable());
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем метод getDbTableStatic
	 * @covers GoodsCatalogAbstractActiveRecord::getDbTableStatic
	 */
	public function testGetDbTableStatic()
	{
		$GLOBALS['Eresus'] = new stdClass();
		$GLOBALS['Eresus']->plugins = new PluginsStub();

		$this->assertEquals('goodscatalog_mytable', GoodsCatalogAbstractActiveRecordTest_Stub::getDbTableStatic('GoodsCatalogAbstractActiveRecordTest_Stub'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем метод getDbTableStatic
	 * @covers GoodsCatalogAbstractActiveRecord::getDbTableStatic
	 * @expectedException EresusTypeException
	 */
	public function testGetDbTableStatic_fail()
	{
		$this->assertEquals('goodscatalog_mytable', GoodsCatalogAbstractActiveRecordTest_Stub::getDbTableStatic('stdClass'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем вброс исключения EresusPropertyNotExistsException при обращении к несуществующему
	 * свойству.
	 *
	 * @covers GoodsCatalogAbstractActiveRecord::__get
	 * @covers GoodsCatalogAbstractActiveRecord::getProperty
	 * @expectedException EresusPropertyNotExistsException
	 */
	public function testGetUnexistentProperty()
	{
		$x = $this->fixture->unexistent;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем чтение неустановленного свойства.
	 *
	 * @covers GoodsCatalogAbstractActiveRecord::__get
	 * @covers GoodsCatalogAbstractActiveRecord::getProperty
	 */
	public function testGetUnsetProperty()
	{
		$this->assertNull($this->fixture->int);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем вброс исключения EresusPropertyNotExistsException при обращении к несуществующему
	 * свойству.
	 *
	 * @covers GoodsCatalogAbstractActiveRecord::__set
	 * @covers GoodsCatalogAbstractActiveRecord::setProperty
	 * @expectedException EresusPropertyNotExistsException
	 */
	public function testSetUnexistentProperty()
	{
		$this->fixture->unexistent = true;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем установку свойства неподдерживаемого типа
	 *
	 * @covers GoodsCatalogAbstractActiveRecord::__set
	 * @covers GoodsCatalogAbstractActiveRecord::setProperty
	 * @expectedException EresusTypeException
	 */
	public function testSetUnsupportedType()
	{
		$this->fixture->unsupported = null;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем установку и чтение полей типа 'bool'
	 *
	 * @covers GoodsCatalogAbstractActiveRecord::getProperty
	 * @covers GoodsCatalogAbstractActiveRecord::setProperty
	 */
	public function testSetGetBool()
	{
		$this->fixture->bool = true;
		$this->assertTrue($this->fixture->bool);
		$this->fixture->bool = false;
		$this->assertFalse($this->fixture->bool);
		$this->fixture->bool = 'yes';
		$this->assertTrue($this->fixture->bool);
		$this->fixture->bool = null;
		$this->assertFalse($this->fixture->bool);
		$this->fixture->bool = '0';
		$this->assertFalse($this->fixture->bool);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем установку и чтение полей типа 'int'
	 *
	 * @covers GoodsCatalogAbstractActiveRecord::getProperty
	 * @covers GoodsCatalogAbstractActiveRecord::setProperty
	 */
	public function testSetGetInt()
	{
		$this->fixture->int = 100;
		$this->assertEquals(100, $this->fixture->int);
		$this->fixture->int = '101';
		$this->assertEquals(101, $this->fixture->int);
		$this->fixture->int = '102abc';
		$this->assertEquals(102, $this->fixture->int);
		$this->fixture->int = 'abc103';
		$this->assertEquals(0, $this->fixture->int);
		$this->fixture->int = '104a105';
		$this->assertEquals(104, $this->fixture->int);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем установку и чтение полей типа 'string'
	 *
	 * @covers GoodsCatalogAbstractActiveRecord::getProperty
	 * @covers GoodsCatalogAbstractActiveRecord::setProperty
	 * @covers GoodsCatalogAbstractActiveRecord::filterString
	 */
	public function testSetGetString()
	{
		$this->fixture->string = 'test1';
		$this->assertEquals('test1', $this->fixture->string);
		$this->fixture->string = '0123456789abc';
		$this->assertEquals('0123456789', $this->fixture->string);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем isNew
	 *
	 * @covers GoodsCatalogAbstractActiveRecord::isNew
	 */
	public function testIsNew()
	{
		$this->assertTrue($this->fixture->isNew());
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем вызов сеттера
	 *
	 * @covers GoodsCatalogAbstractActiveRecord::__set
	 */
	public function testCallSetter()
	{
		$this->fixture->int2 = 123;
		$this->assertTrue($this->fixture->_success);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем вызов геттера
	 *
	 * @covers GoodsCatalogAbstractActiveRecord::__get
	 */
	public function testCallGetter()
	{
		$this->assertEquals(123, $this->fixture->int2);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем сохранение
	 *
	 * @covers GoodsCatalogAbstractActiveRecord::save
	 */
	public function test_save()
	{
		$stub = new GoodsCatalogAbstractActiveRecordTest_Stub();
		DBHandlerStub::reset();
		$stub->save();
		$this->assertEquals(1, DBHandlerStub::$createInsertQuery);
		$this->assertEquals(0, DBHandlerStub::$createUpdateQuery);
		$this->assertEquals(1, DBHandlerStub::$lastInsertId);

		DBHandlerStub::reset();
		$stub->save();
		$this->assertEquals(0, DBHandlerStub::$createInsertQuery);
		$this->assertEquals(1, DBHandlerStub::$createUpdateQuery);
		$this->assertEquals(0, DBHandlerStub::$lastInsertId);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем удаление
	 *
	 * @covers GoodsCatalogAbstractActiveRecord::delete
	 */
	public function test_delete()
	{
		$stub = new GoodsCatalogAbstractActiveRecordTest_Stub();
		DBHandlerStub::reset();
		$stub->delete();
		$this->assertEquals(0, DBHandlerStub::$createDeleteQuery);

		$stub->save();
		DBHandlerStub::reset();
		$stub->delete();
		$this->assertEquals(1, DBHandlerStub::$createDeleteQuery);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем перемещение вверх
	 *
	 * @covers GoodsCatalogAbstractActiveRecord::moveUp
	 */
	public function test_moveUp()
	{
		if (version_compare(PHP_VERSION, '5.3'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}
		$stub = new GoodsCatalogAbstractActiveRecordTest_Stub();
		$stub->save();
		DBHandlerStub::reset();
		$stub->moveUp();
		$this->assertEquals(0, DBHandlerStub::$createUpdateQuery);

		DBHandlerStub::reset();
		$db = $this->getMock('stdClass', array('fetch'));
		$db->expects($this->once())->method('fetch')->will($this->returnValue(array('id' => 1)));
		DB::setMock($db);

		$stub->position = 1;
		$stub->moveUp();
		$this->assertEquals(2, DBHandlerStub::$createUpdateQuery);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем перемещение ввниз
	 *
	 * @covers GoodsCatalogAbstractActiveRecord::moveDown
	 */
	public function test_moveDown()
	{
		if (version_compare(PHP_VERSION, '5.3'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}
		$stub = new GoodsCatalogAbstractActiveRecordTest_Stub();
		$stub->save();
		DBHandlerStub::reset();
		$db = $this->getMock('stdClass', array('fetch'));
		$db->expects($this->once())->method('fetch')->will($this->returnValue(array('id' => 1)));
		DB::setMock($db);
		$stub->moveDown();
		$this->assertEquals(2, DBHandlerStub::$createUpdateQuery);
	}
	//-----------------------------------------------------------------------------

	/* */
}

/*******************************************************************************
 *
 * ЗАГЛУШКИ
 *
 *******************************************************************************/

/**
 * @package GoodsCatalog
 * @subpackage Tests
 */
class GoodsCatalogAbstractActiveRecordTest_Stub extends GoodsCatalogAbstractActiveRecord
{
	public $_success = false;

	public function getTableName()
	{
		return 'mytable';
	}
	//-----------------------------------------------------------------------------

	public function getAttrs()
	{
		return array(
			'unsupported' => array('type' => null),
			'bool' => array('type' => PDO::PARAM_BOOL),
			'int' => array('type' => PDO::PARAM_INT),
			'string' => array('type' => PDO::PARAM_STR, 'maxlength' => 10),

			'id' => array('type' => PDO::PARAM_INT),
			'position' => array('type' => PDO::PARAM_INT),
			'section' => array('type' => PDO::PARAM_INT),
		);
	}
	//-----------------------------------------------------------------------------

	protected function setInt2()
	{
		$this->_success = true;
	}
	//-----------------------------------------------------------------------------

	protected function getInt2()
	{
		return 123;
	}
	//-----------------------------------------------------------------------------
}
