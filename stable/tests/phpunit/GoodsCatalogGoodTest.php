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
include_once dirname(__FILE__) . '/../../src/goodscatalog/classes/Good.php';

/**
 * @package GoodsCatalog
 * @subpackage Tests
 */
class GoodsCatalogGoodTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @link http://bugs.eresus.ru/view.php?id=583
	 *
	 * @covers GoodsCatalogGood::setSection
	 */
	public function test_issue583()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$test = $this->getMock('GoodsCatalogGood', array('setProperty', 'getProperty'), array(), '',
			false);
		$test->expects($this->once())->method('getProperty')->will($this->returnValue(123));

		$rawData = new ReflectionProperty('GoodsCatalogAbstractActiveRecord', 'rawData');
		$rawData->setAccessible(true);
		$rawData->setValue($test, array('section' => 123));

		$originalSection = new ReflectionProperty('GoodsCatalogGood', 'originalSection');
		$originalSection->setAccessible(true);
		$originalSection->setValue($test, null);

		$setSection = new ReflectionMethod('GoodsCatalogGood', 'setSection');
		$setSection->setAccessible(true);
		$setSection->invoke($test, 123);

		$this->assertNull($originalSection->getValue($test));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @link http://bugs.eresus.ru/view.php?id=689
	 *
	 * @covers GoodsCatalogGood::getAttrs
	 */
	public function test_issue689()
	{
		$test = new GoodsCatalogGood();
		$fields = $test->getAttrs();
		foreach ($fields as $name => $attrs)
		{
			if (isset($attrs['maxlength']))
			{
				$this->assertInternalType('integer', $attrs['maxlength'], $name);
			}
		}
	}
	//-----------------------------------------------------------------------------
}
