<?php
/**
 * Каталог товаров
 *
 * Интерфейс управления брендами
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
 * Интерфейс управления брендами
 *
 * @package GoodsCatalog
 */
class GoodsCatalog_BrandsAdminUI extends GoodsCatalog_AbstractAdminUI
{
	/**
	 * @return string
	 *
	 * @see GoodsCatalog_AbstractAdminUI::getActiveRecordClass()
	 */
	protected function getActiveRecordClass()
	{
		return 'GoodsCatalog_Brand';
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает разметку интерфейса списка брендов
	 *
	 * @return string  HTML
	 *
	 * @since 1.00
	 */
	protected function renderList()
	{
		/** @var TAdminUI $page */
		$page = Eresus_Kernel::app()->getPage();

		// Данные для подстановки в шаблон
		$data = $this->plugin->getHelper()->prepareTmplData();

		/* Шаблоны адресов действий */
		$data['urlEdit'] = str_replace('&', '&amp;', $page->url(array('id' => '%s')));
		$data['urlToggle'] = str_replace('&', '&amp;', $page->url(array('toggle' => '%s')));
		$data['urlDelete'] = str_replace('&', '&amp;', $page->url(array('delete' => '%s')));

		// Определяем текущую страницу списка
		$pg = arg('pg') ? arg('pg', 'int') : 1;
		$maxCount = 10; // Количество групп на страницу. В настройках не изменяется.
		$startFrom = ($pg - 1) * $maxCount;

		$data['brands'] = GoodsCatalog_Brand::find($maxCount, $startFrom);
		$totalPages = ceil(GoodsCatalog_Brand::count() / $maxCount);
		if ($totalPages > 1)
		{
			$data['pagination'] = new PaginationHelper($totalPages, $pg, $page->url(array('pg' => '%s')));
		}
		else
		{
			$data['pagination'] = null;
		}

		// Создаём экземпляр шаблона
		$tmpl = $this->plugin->getHelper()->getAdminTemplate('brands-list.html');

		// Компилируем шаблон и данные
		$html = $tmpl->compile($data);

		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Удаляет бренд
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
			$brand = new GoodsCatalog_Brand($id);

			try
			{
				$brand->delete();
			}
			catch (Exception $e)
			{
				ErrorMessage('Не удалось удалить бренд: ' .	$e->getMessage());
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
	 * Возвращает разметку диалога добавления бренда
	 *
	 * @return string  HTML
	 *
	 * @since 1.00
	 */
	protected function renderAddDialog()
	{
		// Данные для подстановки в шаблон
		$data = $this->plugin->getHelper()->prepareTmplData();

		// Создаём экземпляр шаблона
		$tmpl = $this->plugin->getHelper()->getAdminTemplate('brands-add.html');

		// Компилируем шаблон и данные
		$html = $tmpl->compile($data);

		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Добавляет бренд
	 *
	 * @return void
	 *
	 * @since 1.00
	 * @uses HTTP::redirect
	 */
	protected function addItem()
	{
		$brand = new GoodsCatalog_Brand();
		$brand->title = arg('title');
		$brand->active = true;
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
			ErrorMessage('Произошла внутренняя ошибка при добавлении бренда.');
		}

		HTTP::redirect('admin.php?mod=ext-' . $this->plugin->name . '&ref=brands');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает разметку диалога изменения бренда
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
			$brand = new GoodsCatalog_Brand($id);
		}
		catch (DomainException $e)
		{
			$this->reportBadURL($e);
			return;
		}

		// Данные для подстановки в шаблон
		$data = $this->plugin->getHelper()->prepareTmplData();
		$data['brand'] = $brand;

		// Создаём экземпляр шаблона
		$tmpl = $this->plugin->getHelper()->getAdminTemplate('brands-edit.html');

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
			$brand = new GoodsCatalog_Brand($id);
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
			ErrorMessage('Произошла внутренняя ошибка при изменении бренда.');
		}

		HTTP::redirect('admin.php?mod=ext-' . $this->plugin->name . '&ref=brands');
	}
	//-----------------------------------------------------------------------------
}