<?php
/**
 * Каталог товаров
 *
 * Интерфейс управления товарами
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
 * Интерфейс управления товарами
 *
 * @package GoodsCatalog
 */
class GoodsCatalogGoodsAdminUI extends GoodsCatalogAbstractAdminUI
{
	/**
	 * (non-PHPdoc)
	 * @see src/goodscatalog/classes/GoodsCatalogAbstractAdminUI::getActiveRecordClass()
	 */
	protected function getActiveRecordClass()
	{
		return 'GoodsCatalogGood';
	}
	//-----------------------------------------------------------------------------

	/**
	 * (non-PHPdoc)
	 * @see src/goodscatalog/classes/GoodsCatalogAbstractAdminUI::extendedActions()
	 */
	protected function extendedActions()
	{
		switch (true)
		{
			case arg('up'):
				$this->moveUp();
			break;

			case arg('down'):
				$this->moveDown();
			break;

			default:
				return false;
			break;
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Отрисовывает интерфейс списка товаров
	 *
	 * @return string  HTML
	 *
	 * @since 1.00
	 */
	protected function renderList()
	{
		global $page;

		// Данные для подстановки в шаблон
		$data = $this->plugin->getHelper()->prepareTmplData();
		$data['section'] = arg('section', 'int');

		/* Шаблоны адресов действий */
		$data['urlEdit'] = str_replace('&', '&amp;', $page->url(array('id' => '%s')));
		$data['urlToggle'] = str_replace('&', '&amp;', $page->url(array('toggle' => '%s')));
		$data['urlUp'] = str_replace('&', '&amp;', $page->url(array('up' => '%s')));
		$data['urlDown'] = str_replace('&', '&amp;', $page->url(array('down' => '%s')));
		$data['urlDelete'] = str_replace('&', '&amp;', $page->url(array('delete' => '%s')));

		// Определяем текущую страницу списка
		$pg = arg('pg') ? arg('pg', 'int') : 1;
		$maxCount = $this->plugin->settings['goodsPerPage'];
		$startFrom = ($pg - 1) * $maxCount;

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

		// Создаём экземпляр шаблона
		$tmpl = $this->plugin->getHelper()->getAdminTemplate('goods-list.html');

		// Компилируем шаблон и данные
		$html = $tmpl->compile($data);

		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает диалог добавления товара
	 *
	 * @return string  HTML
	 *
	 * @since 1.00
	 */
	protected function renderAddDialog()
	{
		/*
		 * Имитируем использование старых форм на основе массивов.
		 * Это требуется для правильного подключения WYSIWYG.
		 */
		$wysiwyg = $GLOBALS['Eresus']->extensions->load('forms', 'html');
		$fakeForm = array('values' => array());
		$fakeField = array(
			'name' => 'description',
			'value' => '',
			'label' => '',
			'height' => null,
		);
		$wysiwyg->forms_html($fakeForm, $fakeField);

		// Данные для подстановки в шаблон
		$data = $this->plugin->getHelper()->prepareTmplData();
		$data['section'] = arg('section', 'int');
		$data['brands'] = GoodsCatalogBrand::find(null, null, true);

		// Создаём экземпляр шаблона
		$tmpl = $this->plugin->getHelper()->getAdminTemplate('goods-add.html');

		// Компилируем шаблон и данные
		$html = $tmpl->compile($data);

		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Добавляет товар
	 *
	 * @return void
	 *
	 * @since 1.00
	 * @uses HTTP::redirect
	 */
	protected function addItem()
	{
		$good = new GoodsCatalogGood();
		$good->section = arg('section', 'int');
		$good->article = arg('article');
		$good->title = arg('title');
		$good->about = arg('about');
		$good->description = arg('description');
		$good->cost = arg('cost');
		$good->active = arg('active', 'int');
		$good->special = arg('special', 'int');
		$good->description = arg('description');
		$good->brand = arg('brand', 'int');
		$good->photo = 'photo'; // $_FILES['photo'];
		try
		{
			$good->save();
		}
		catch (EresusRuntimeException $e)
		{
			ErrorMessage($e->getMessage());
		}
		catch (Exception $e)
		{
			Core::logException($e);
			ErrorMessage(iconv('utf8', 'cp1251', 'Произошла внутренняя ошибка при добавлении товара.'));
		}
		HTTP::redirect('admin.php?mod=content&section=' . $good->section);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Удаляет товар
	 *
	 * @return void
	 *
	 * @since 1.00
	 */
	protected function deleteItem()
	{
		$id = arg('delete', 'int');

		try
		{
			$good = new GoodsCatalogGood($id);

			try
			{
				$good->delete();
			}
			catch (Exception $e)
			{
				ErrorMessage(iconv('utf8', 'cp1251', 'Не удалось удалить товар: ') .
					$e->getMessage());
			}
		}
		catch (DomainException $e)
		{
			$this->reportBadURL($e);
		}

		HTTP::goback();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает разметку диалога изменения товара
	 *
	 * @return string  HTML
	 *
	 * @since 1.00
	 */
	protected function renderEditDialog()
	{
		$id = arg('id', 'int');

		try
		{
			$good = new GoodsCatalogGood($id);
		}
		catch (DomainException $e)
		{
			$this->reportBadURL($e);
			return;
		}

		/*
		 * Имитируем использование старых форм на основе массивов.
		 * Это требуется для правильного подключения WYSIWYG.
		 */
		$wysiwyg = $GLOBALS['Eresus']->extensions->load('forms', 'html');
		$fakeForm = array('values' => array());
		$fakeField = array(
			'name' => 'description',
			'value' => '',
			'label' => '',
			'height' => null,
		);
		$wysiwyg->forms_html($fakeForm, $fakeField);

		// Данные для подстановки в шаблон
		$data = $this->plugin->getHelper()->prepareTmplData();
		$data['good'] = $good;
		$data['brands'] = GoodsCatalogBrand::find(null, null, true);

		$data['sections'] = $this->buildSectionTree();

		// Создаём экземпляр шаблона
		$tmpl = $this->plugin->getHelper()->getAdminTemplate('goods-edit.html');

		// Компилируем шаблон и данные
		$html = $tmpl->compile($data);

		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Изменяет бренд
	 *
	 * @return void
	 *
	 * @since 1.00
	 * @uses HTTP::redirect
	 */
	protected function updateItem()
	{
		$id = arg('update', 'int');
		try
		{
			$brand = new GoodsCatalogBrand($id);
		}
		catch (DomainException $e)
		{
			$this->reportBadURL($e);
		}

		$brand->title = arg('title');
		$brand->description = arg('description');
		$brand->logo = 'logo'; // $_FILES['image'];
		try
		{
			$brand->save();
		}
		catch (EresusRuntimeException $e)
		{
			ErrorMessage($e->getMessage());
		}
		catch (Exception $e)
		{
			Core::logException($e);
			ErrorMessage(iconv('utf8', 'cp1251', 'Произошла внутренняя ошибка при изменении бренда.'));
		}

		HTTP::redirect('admin.php?mod=ext-' . $this->plugin->name . '&ref=brands');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Перемещение товара вверх по списку
	 *
	 * @return void
	 */
	private function moveUp()
	{
		try
		{
			$good = new GoodsCatalogGood(arg('up', 'int'));
		}
		catch (DomainException $e)
		{
			$this->reportBadURL($e);
		}

		$good->moveUp();
		HTTP::goback();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Перемещение товара вниз по списку
	 *
	 * @return void
	 */
	private function moveDown()
	{
		try
		{
			$good = new GoodsCatalogGood(arg('down', 'int'));
		}
		catch (DomainException $e)
		{
			$this->reportBadURL($e);
		}

		$good->moveDown();
		HTTP::goback();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает массив разделов
	 *
	 * @param int $root[optional]  Идентификатор корневого раздела
	 *
	 * @return array
	 *
	 * @since 1.00
	 */
	private function buildSectionTree($root = 0)
	{
		global $Eresus;

		$sections = $Eresus->sections->children(0);
		$result = array();
		foreach ($sections as $section)
		{
			$children = $this->buildSectionTree($section['id']);
			if ($section['type'] == $this->plugin->name || $children)
			{
				$result []= $section;
				if ($children)
				{
					$result = array_merge($result, $children);
				}
			}
		}
		return $result;
	}
	//-----------------------------------------------------------------------------
}