<?php
/**
 * Интерфейс просмотра товаров
 *
 * @version ${product.version}
 *
 * @copyright 2010, ООО "Два слона", http://dvaslona.ru/
 * @license http://www.gnu.org/licenses/gpl.txt GPL License 3
 * @author Михаил Красильников <mk@dvaslona.ru>
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
 */


/**
 * Клиентский интерфейс к товарам
 *
 * @package GoodsCatalog
 */
class GoodsCatalog_GoodsClientUI extends GoodsCatalog_AbstractUI
{
    /**
     * @return string
     *
     * @see GoodsCatalog_AbstractUI::getActiveRecordClass()
     */
    protected function getActiveRecordClass()
    {
        return 'GoodsCatalog_Good';
    }

    /**
     * Возвращает HTML интерфейса
     *
     * @return string
     */
    public function getHTML()
    {
        /** @var TClientUI $page */
        $page = Eresus_Kernel::app()->getPage();

        if ($page->topic)
        {
            $html = $this->renderItem();
        }
        else
        {
            $html = $this->renderList();
        }

        return $html;
    }

    /**
     * Отрисовывает интерфейс списка товаров
     *
     * @return string  HTML
     *
     * @since 1.00
     */
    private function renderList()
    {
        /** @var TClientUI $page */
        $page = Eresus_Kernel::app()->getPage();

        // Данные для подстановки в шаблон
        $data = $this->plugin->getHelper()->prepareTmplData();
        // Определяем текущую страницу списка
        $pg = $page->subpage ? $page->subpage : 1;
        $maxCount = $this->plugin->settings['goodsPerPage'];
        $startFrom = ($pg - 1) * $maxCount;

        $data['goods'] = GoodsCatalog_Good::find($page->id, $maxCount, $startFrom, true);
        $totalPages = ceil(GoodsCatalog_Good::count($page->id, true) / $maxCount);

        if ($pg > $totalPages && $pg != 1)
        {
            throw new Eresus_HTTP_Exception_NotFound;
        }

        if ($totalPages > 1)
        {
            $data['pagination'] = new PaginationHelper($totalPages, $pg);
        }
        else
        {
            $data['pagination'] = null;
        }

        // Создаём экземпляр шаблона
        $tmpl = $this->plugin->templates()->client('goods-list.html');

        // Компилируем шаблон и данные
        $html = $tmpl->compile($data);

        return $html;
    }

    /**
     * Возвращает разметку описания товара
     *
     * @throws DomainException
     *
     * @return string  HTML
     *
     * @since 1.00
     */
    private function renderItem()
    {
        /** @var TClientUI $page */
        $page = Eresus_Kernel::app()->getPage();

        try
        {
            $good = new GoodsCatalog_Good(intval($page->topic));
            if (!$good->active)
            {
                throw new DomainException;
            }
        }
        catch (DomainException $e)
        {
            throw new Eresus_HTTP_Exception_NotFound;
        }
        // Данные для подстановки в шаблон
        $data = $this->plugin->getHelper()->prepareTmplData();
        $data['good'] = $good;
        $data['listURL'] = $page->clientURL($page->id);
        if ($page instanceof TClientUI && $page->subpage)
        {
            $data['listURL'] .= 'p' . $page->subpage . '/';
        }

        // Создаём экземпляр шаблона
        $tmpl = $this->plugin->templates()->client('goods-item.html');

        // Компилируем шаблон и данные
        $html = $tmpl->compile($data);

        $this->plugin->getHelper()->linkJQuery();
        $this->plugin->getHelper()->linkJQueryUI();
        $page->linkScripts($this->plugin->getCodeURL() . 'client.js');
        $page->linkStyles($this->plugin->getCodeURL() . 'client.css');

        return $html;
    }
}

