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

PHP_CodeCoverage_Filter::getInstance()->addFileToBlacklist(__FILE__);

$root = realpath(dirname(__FILE__) . '/../../src');

PHP_CodeCoverage_Filter::getInstance()->addDirectoryToWhitelist($root);
PHP_CodeCoverage_Filter::getInstance()->removeFileFromWhitelist($root . '/goodscatalog/autoload.php');
PHP_CodeCoverage_Filter::getInstance()->removeFileFromWhitelist($root . '/goodscatalog/classes/TemplateService.php');

require_once dirname(__FILE__) . '/helpers.php';

require_once dirname(__FILE__) . '/GoodsCatalogMoneyTest.php';
require_once dirname(__FILE__) . '/GoodsCatalogAbstractActiveRecordTest.php';
require_once dirname(__FILE__) . '/GoodsCatalogGoodTest.php';
require_once dirname(__FILE__) . '/GoodsCatalogAbstractUITest.php';
require_once dirname(__FILE__) . '/GoodsCatalogAbstractAdminUITest.php';
require_once dirname(__FILE__) . '/GoodsCatalogGoodsAdminUITest.php';
require_once dirname(__FILE__) . '/GoodsCatalogGoodsClientUITest.php';

class AllTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('All Tests');

		$suite->addTestSuite('GoodsCatalogMoneyTest');
		$suite->addTestSuite('GoodsCatalogAbstractActiveRecordTest');
		$suite->addTestSuite('GoodsCatalogGoodTest');
		$suite->addTestSuite('GoodsCatalogAbstractUITest');
		$suite->addTestSuite('GoodsCatalogAbstractAdminUITest');
		$suite->addTestSuite('GoodsCatalogGoodsAdminUITest');
		$suite->addTestSuite('GoodsCatalogGoodsClientUITest');
		return $suite;
	}
}
