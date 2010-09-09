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
class GoodsCatalogBrandsAdminUI
{
	/**
	 * Объект плагина
	 *
	 * @var GoodsCatalog
	 */
	private $plugin;

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
	private function reportBadURL(Exception $e)
	{
		ErrorMessage(iconv('utf8', 'cp1251', 'Неправильный адрес'));

		return;
		$e = $e; // PHPMD hack
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает разметку интерфейса списка брендов
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

		/* Шаблоны адресов действий */
		$data['urlEdit'] = str_replace('&', '&amp;', $page->url(array('id' => '%s')));
		$data['urlToggle'] = str_replace('&', '&amp;', $page->url(array('toggle' => '%s')));
		$data['urlDelete'] = str_replace('&', '&amp;', $page->url(array('delete' => '%s')));

		// Определяем текущую страницу списка
		$pg = arg('pg') ? arg('pg', 'int') : 1;
		$maxCount = 10;
		$startFrom = ($pg - 1) * $maxCount;

		$data['brands'] = GoodsCatalogBrand::find($maxCount, $startFrom);
		$totalPages = ceil(GoodsCatalogBrand::count() / $maxCount);
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
	 * Переключает активность бренда
	 *
	 * @return void
	 *
	 * @since 1.00
	 */
	private function toggleItem()
	{
		$id = arg('toggle', 'int');

		try
		{
			$brand = new GoodsCatalogBrand($id);

			try
			{
				$brand->active = ! $brand->active;
				$brand->save();
			}
			catch (Exception $e)
			{
				ErrorMessage(iconv('utf8', 'cp1251', 'Не удалось сохранить изменения: ') .
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
	 * Удаляет бренд
	 *
	 * @return void
	 *
	 * @since 1.00
	 */
	private function deleteItem()
	{
		$id = arg('delete', 'int');

		try
		{
			$brand = new GoodsCatalogBrand($id);

			try
			{
				$brand->delete();
			}
			catch (Exception $e)
			{
				ErrorMessage(iconv('utf8', 'cp1251', 'Не удалось удалить бренд: ') .
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
	 * Возвращает разметку диалога добавления бренда
	 *
	 * @return string  HTML
	 *
	 * @since 1.00
	 */
	private function renderAddDialog()
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
	private function addItem()
	{
		$brand = new GoodsCatalogBrand();
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
			ErrorMessage(iconv('utf8', 'cp1251', 'Произошла внутренняя ошибка при добавлении бренда.'));
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
	private function renderEditDialog()
	{
		$id = arg('id', 'int');

		try
		{
			$brand = new GoodsCatalogBrand($id);
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
	private function updateItem()
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
}