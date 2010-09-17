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

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

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



/**
 * @package GoodsCatalog
 * @subpackage Tests
 */
class PluginsStub
{
	public $plugin;

	public function __construct()
	{
		$this->plugin = new GoodsCatalog();
	}
	//-----------------------------------------------------------------------------

	public function __destruct()
	{
		unset($this->plugin);
	}
	//-----------------------------------------------------------------------------

	public function load($name)
	{
		return $this->plugin;
	}
	//-----------------------------------------------------------------------------
}



/**
 * @package GoodsCatalog
 * @subpackage Tests
 */
class GoodsCatalog
{
	public $name = 'goodscatalog';

	public $settings = array(
		'showItemMode' => 'default'
	);

	public function getDataURL()
	{
		return 'http://example.org/data/name/';
	}
	//-----------------------------------------------------------------------------

	public function clientListURL()
	{
		return 'http://example.org/name/';
	}
	//-----------------------------------------------------------------------------
}


function eresus_log()
{
	;
}
//-----------------------------------------------------------------------------


class DB
{
	public static function getHandler()
	{
		return new DBHandlerStub();
	}
	//-----------------------------------------------------------------------------

	public static function execute()
	{
		return null;
	}
	//-----------------------------------------------------------------------------

	public static function fetch()
	{
		return array('id' => 1);
	}
	//-----------------------------------------------------------------------------
}

class DBHandlerStub
{
	public static $createDeleteQuery = 0;
	public static $createInsertQuery = 0;
	public static $createSelectQuery = 0;
	public static $createUpdateQuery = 0;
	public static $lastInsertId = 0;

	public static function reset()
	{
		self::$createDeleteQuery = 0;
		self::$createInsertQuery = 0;
		self::$createSelectQuery = 0;
		self::$createUpdateQuery = 0;
		self::$lastInsertId = 0;
	}
	//-----------------------------------------------------------------------------

	public function createDeleteQuery()
	{
		self::$createDeleteQuery++;
		return new Fluent();
	}
	//-----------------------------------------------------------------------------

	public function createInsertQuery()
	{
		self::$createInsertQuery++;
		return new Fluent();
	}
	//-----------------------------------------------------------------------------

	public function createSelectQuery()
	{
		self::$createSelectQuery++;
		return new Fluent();
	}
	//-----------------------------------------------------------------------------

	public function createUpdateQuery()
	{
		self::$createUpdateQuery++;
		return new Fluent();
	}
	//-----------------------------------------------------------------------------

	public function lastInsertId()
	{
		self::$lastInsertId++;
		return 1;
	}
	//-----------------------------------------------------------------------------
}


class Fluent
{
	public function __get($a)
	{
		return $this;
	}
	//-----------------------------------------------------------------------------

	public function __call($a, $b)
	{
		return $this;
	}
	//-----------------------------------------------------------------------------
}

class ezcQuerySelect
{
	const ASC = 'ASC';
	const DESC = 'DESC';
}