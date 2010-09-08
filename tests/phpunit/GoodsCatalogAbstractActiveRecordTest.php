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

include_once dirname(__FILE__) . '/../../src/goodscatalog/AbstractActiveRecord.php';

/**
 * @package GoodsCatalog
 * @subpackage Tests
 */
class GoodsCatalogAbstractActiveRecordTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Setup test enviroment
	 */
	protected function setUp()
	{
		$this->fixture = new GoodsCatalogAbstractActiveRecord_Stub(new GoodsCatalog());
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем вброс исключения EresusPropertyNotExistsException при обращении к несуществующему
	 * свойству.
	 *
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
	 */
	public function testIsNew()
	{
		$this->assertTrue($this->fixture->isNew());
	}
	//-----------------------------------------------------------------------------

	/* */
}

/**
 * @package GoodsCatalog
 * @subpackage Tests
 */
class GoodsCatalogAbstractActiveRecord_Stub extends GoodsCatalogAbstractActiveRecord
{
	/**
	 * Метод возвращает имя таблицы БД
	 *
	 * @return string  Имя таблицы БД
	 */
	protected function getTableName()
	{
		return 'brands';
	}
	//-----------------------------------------------------------------------------

	/**
	 * Метод возвращает список полей записи и их атрибуты
	 *
	 * @return array
	 */
	protected function getFieldAttrs()
	{
		return array(
			'unsupported' => array('type' => null),
			'bool' => array('type' => PDO::PARAM_BOOL),
			'int' => array('type' => PDO::PARAM_INT),
			'string' => array('type' => PDO::PARAM_STR, 'maxlength' => 10),
		);
	}
	//-----------------------------------------------------------------------------
}



/**
 * @package GoodsCatalog
 * @subpackage Tests
 */
class GoodsCatalog
{
}

/**
 * @package GoodsCatalog
 * @subpackage Tests
 */
class EresusPropertyNotExistsException extends Exception
{
	function __construct($property = null, $class = null, $description = null, $previous = null)
	{
	}
	//-----------------------------------------------------------------------------
}

/**
 * @package GoodsCatalog
 * @subpackage Tests
 */
class EresusTypeException extends Exception
{
	function __construct($var = null, $expectedType = null, $description = null, $previous = null)
	{
	}
	//-----------------------------------------------------------------------------
}
