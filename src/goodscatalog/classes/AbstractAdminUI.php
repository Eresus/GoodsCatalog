<?php
/**
 * Абстрактный пользовательский интерфейс управления (АИ)
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
 * Абстрактный пользовательский интерфейс управления (АИ)
 *
 * @package GoodsCatalog
 */
abstract class GoodsCatalog_AbstractAdminUI extends GoodsCatalog_AbstractUI
{
    /**
     * Возвращает HTML интерфейса управления
     *
     * Метод "понимает" HTTP-запросы на следующие базовые действия:
     *
     * - Добавление объекта. Аргумент "action" должен быть "insert". Будет вызван метод addItem
     * - Переключение активности. Аргумент "toggle" должен содержать ID объекта. Вызовет toggleItem
     * - Удаление. Аргумент "delete" должен содержать ID объекта. Вызовет deleteItem
     * - Изменение объекта. Аргумент "update" должен содержать ID объекта. Вызовет updateItem
     * - Диалог добавления. Аргумент "action" должен быть "add". Вызовет renderAddDialog
     * - Диалог изменения. Аргумент "id" должен содержать ID объекта. Вызовет renderEditDialog
     *
     * Если ни одно из действий не подошло и метод {@link extendedActions} вернул FALSE, вызовет
     * метод renderList для отрисовки списка объектов.
     *
     * Кроме этого метод подключает к странице admin.js и admin.css
     *
     * @return string
     *
     * @uses $page
     * @uses $plugin
     * @uses arg()
     * @uses addItem()
     * @uses deleteItem()
     * @uses GoodsCatalog::getCodeURL()
     * @uses renderAddDialog()
     * @uses renderEditDialog()
     * @uses renderList()
     * @uses toggleItem()
     * @uses updateItem()
     * @uses WebPage::linkScripts()
     * @uses WebPage::linkStyles()
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
                $html = $this->extendedActions();
                if ($html === false)
                {
                    $html = $this->renderList();
                }
                break;
        }

        /* Дополнительные файлы */
        /** @var TAdminUI $page */
        $page = Eresus_Kernel::app()->getPage();
        $page->linkStyles($this->plugin->getCodeURL() . 'admin.css');
        $page->linkScripts($this->plugin->getCodeURL() . 'admin.js');

        return $html;
    }

    /**
     * Помещает в сессию сообщение о неправильном адресе
     *
     * @param Exception $e
     *
     * @return void
     *
     * @uses ErrorMessage()
     */
    protected function reportBadURL(Exception $e)
    {
        ErrorMessage('Неправильный адрес');
    }

    /**
     * Потомки могут перекрывать этот метод для добавления дополнительных действий над объектами
     *
     * Список основных действий см. в описании {@link getHTML()}.
     *
     * Метод должен возвращать HTML или FALSE если ни одно действие не выполнено.
     *
     * return false
     */
    protected function extendedActions()
    {
        return false;
    }

    abstract protected function addItem();

    /**
     * Переключает активность объекта
     *
     * @return void
     *
     * @uses arg()
     * @uses GoodsCatalog_AbstractActiveRecord::save()
     * @uses ErrorMessage()
     * @uses HTTP::goback()
     * @uses reportBadURL()
     * @since 1.00
     */
    protected function toggleItem()
    {
        $id = arg('toggle', 'int');

        try
        {
            $class = $this->activeRecordClass;
            /** @var GoodsCatalog_AbstractActiveRecord $item */
            $item = new $class($id);

            try
            {
                $item->active = !$item->active;
                $item->save();
            }
            catch (Exception $e)
            {
                ErrorMessage('Не удалось сохранить изменения: ' . $e->getMessage());
            }
        }
        catch (DomainException $e)
        {
            $this->reportBadURL($e);
        }

        HTTP::goback();
    }

    abstract protected function deleteItem();

    abstract protected function updateItem();

    abstract protected function renderAddDialog();

    abstract protected function renderEditDialog();

    abstract protected function renderList();
}

