<?php
/**
 * Таблица товаров
 *
 * @version ${product.version}
 *
 * @copyright 2012, ООО "Два слона", http://dvaslona.ru/
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
 * $Id: GoodsClientUI.php 1510 2012-05-19 08:15:15Z mk $
 */


/**
 * Товар
 *
 * @package GoodsCatalog
 */
class GoodsCatalog_Entity_Table_Good extends ORM_Table
{
	/**
	 * Объявление свойств таблицы
	 */
	public function setTableDefinition()
	{
		$this->setTableName('goodscatalog_goods');
		$this->hasColumns(
			array(
				'id' => array(
					'type' => 'integer',
					'unsigned' => true,
					'autoincrement' => true,
				),
				'section' => array(
					'type' => 'integer',
					'unsigned' => true,
				),
				'active' => array(
					'type' => 'boolean',
				),
				'position' => array(
					'type' => 'integer',
					'unsigned' => true,
				),
				'article' => array(
					'type' => 'string',
					'length' => 255,
				),
				'title' => array(
					'type' => 'string',
					'length' => 255,
				),
				'about' => array(
					'type' => 'string',
					'length' => 65535,
				),
				'description' => array(
					'type' => 'string',
					'length' => 2147483647,
				),
				'cost' => array(
					'type' => 'string',
					'length' => 10,
				),
				'ext' => array(
					'type' => 'string',
					'maxlength' => 4,
				),
				'special' => array(
					'type' => 'boolean',
				),
				'brand' => array(
					'type' => 'integer',
				),
			)
		);
		$this->index('admin_idx', array('fields' => array('section', 'position')));
		$this->index('client_idx', array('fields' => array('section', 'active', 'position')));
	}
	//-----------------------------------------------------------------------------
}
