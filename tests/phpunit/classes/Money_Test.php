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
include_once TESTS_SRC_DIR . '/goodscatalog/classes/Money.php';

class GoodsCatalog_Money_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers GoodsCatalogMoney::setAmount
	 */
	public function test_setAmount()
	{
		$test = new GoodsCatalogMoney;
		$test->setAmount(12345.67);
		$this->assertAttributeSame(12345.67, 'amount', $test, 'Numeric test');

		setlocale(LC_ALL, 'ru_RU.UTF-8');

		$test = new GoodsCatalogMoney;
		$test->setAmount('12345,67');
		$this->assertAttributeSame(12345.67, 'amount', $test, 'Colon test');

		$test = new GoodsCatalogMoney;
		$test->setAmount('12 345');
		$this->assertAttributeSame(floatval(12345), 'amount', $test, 'Spaces test');

		$test = new GoodsCatalogMoney;
		$test->setAmount('12 345 руб');
		$this->assertAttributeSame(floatval(12345), 'amount', $test, 'Currency suffix test');

		setlocale(LC_ALL, 'en_US');

		$test = new GoodsCatalogMoney;
		$test->setAmount('$12 345');
		$this->assertAttributeSame(floatval(12345), 'amount', $test, 'Currency prefix test');

		$a = new GoodsCatalogMoney(12345.67);
		$test = new GoodsCatalogMoney();
		$test->setAmount($a);
		$this->assertAttributeSame(12345.67, 'amount', $test, 'Currency object test');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers GoodsCatalogMoney::__toString
	 */
	public function test_toString()
	{
		setlocale(LC_ALL, 'ru_RU');
		$lc = localeconv();

		$test = new GoodsCatalogMoney(12345.67);
		$this->assertEquals('12' . $lc['mon_thousands_sep'] . '345' . $lc['mon_decimal_point'] . '67',
			strval($test));

		$test = new GoodsCatalogMoney(12345);
		$this->assertEquals('12' . $lc['mon_thousands_sep'] . '345', strval($test));
	}
	//-----------------------------------------------------------------------------

}