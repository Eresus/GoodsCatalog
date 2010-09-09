<?php
/**
 * Каталог товаров
 *
 * Абстрактный интерфейс управления
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
 * Абстрактный интерфейс управления
 *
 * @package GoodsCatalog
 */
class GoodsCatalogAbstractAdminUI
{
	/**
	 * Объект плагина
	 *
	 * @var GoodsCatalog
	 */
	protected $plugin;

	/**
	 * Конструктор
	 *
	 * @param GoodsCatalog $plugin
	 *
	 * @return GoodsCatalogBrandsAI
	 */
	public function __construct(GoodsCatalog $plugin)
	{
		$this->plugin = $plugin;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает HTML интерфейса управления брендами
	 *
	 * @return string
	 */
	public function getHTML()
	{
		switch (true)
		{
			case arg('action') == 'insert':
				$this->addItem();
			break;

			case arg('toggle'):
				$this->toggleItem();
			break;

			case arg('delete'):
				$this->deleteItem();
			break;

			case arg('update'):
				$this->updateItem();
			break;

			case arg('action') == 'add':
				$html = $this->renderAddDialog();
			break;

			case arg('id'):
				$html = $this->renderEditDialog();
			break;

			default:
				$html = $this->renderList();
			break;
		}

		/* Дополнительные файлы */
		$GLOBALS['page']->linkStyles($this->plugin->getCodeURL() . 'admin.css');
		$GLOBALS['page']->linkScripts($this->plugin->getCodeURL() . 'admin.js');

		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Помещает в сессию сообщение о неправильном адресе
	 *
	 * @param Exception $e
	 */
	protected function reportBadURL(Exception $e)
	{
		ErrorMessage(iconv('utf8', 'cp1251', 'Неправильный адрес'));

		return;
		$e = $e; // PHPMD hack
	}
	//-----------------------------------------------------------------------------

}