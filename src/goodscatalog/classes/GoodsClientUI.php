<?php
/**
 * Каталог товаров
 *
 * Интерфейс просмотра товаров
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
 * Клиентский интерфейс к товарам
 *
 * @package GoodsCatalog
 */
class GoodsCatalogGoodsClientUI
extends GoodsCatalogAbstractUI
{
	/**
	 * (non-PHPdoc)
	 * @see src/goodscatalog/classes/GoodsCatalogAbstractUI::getActiveRecordClass()
	 */
	protected function getActiveRecordClass()
	{
		return 'GoodsCatalogGood';
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает HTML интерфейса
	 *
	 * @return string
	 */
	public function getHTML()
	{
		$html = $this->renderList();
		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Отрисовывает интерфейс списка товаров
	 *
	 * @return string  HTML
	 *
	 * @since 1.00
	 */
	private function renderList()
	{
		global $page;

		// Данные для подстановки в шаблон
		$data = $this->plugin->getHelper()->prepareTmplData();
		// Определяем текущую страницу списка
		//$pg = arg('pg') ? arg('pg', 'int') : 1;
		$maxCount = $this->plugin->settings['goodsPerPage'];
		$startFrom = 0; //($pg - 1) * $maxCount;

		$data['goods'] = GoodsCatalogGood::find($page->id, $maxCount, $startFrom, true);
		/*

		$data['goods'] = GoodsCatalogGood::find($data['section'], $maxCount, $startFrom);
		$totalPages = ceil(GoodsCatalogGood::count($data['section']) / $maxCount);
		if ($totalPages > 1)
		{
			$data['pagination'] = new PaginationHelper($totalPages, $pg, $page->url(array('pg' => '%s')));
		}
		else
		{
			$data['pagination'] = null;
		}
		*/

		// Создаём экземпляр шаблона
		$tmpl = $this->plugin->getHelper()->getClientTemplate('goods-list.html');

		// Компилируем шаблон и данные
		$html = $tmpl->compile($data);

		return $html;
	}
	//-----------------------------------------------------------------------------

}