<?php
/**
 * Каталог товаров
 *
 * ActiveRecord бренда
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
 *
 * $Id$
 */


/**
 * ActiveRecord бренда
 *
 * @property string $title        Название
 * @property bool   $active       Активность бренда
 * @property string $description  Описание бренда
 *
 * @package GoodsCatalog
 */
class GoodsCatalogBrand extends GoodsCatalogAbstractActiveRecord
{
	/**
	 * Метод возвращает имя таблицы БД
	 *
	 * @return string  Имя таблицы БД
	 *
	 * @since 1.00
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
	 *
	 * @since 1.00
	 */
	protected function getFieldAttrs()
	{
		return array(
			'id' => array(
				'type' => 'int',
			),
			'active' => array(
				'type' => 'bool',
			),
			'title' => array(
				'type' => 'string',
				'maxlength' => 4,
			),
			'description' => array(
				'type' => 'string'
			),
			'ext' => array(
				'type' => 'string',
				'maxlength' => 4,
			),
		);
	}
	//-----------------------------------------------------------------------------

}
