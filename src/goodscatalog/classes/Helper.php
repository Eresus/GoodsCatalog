<?php
/**
 * Класс-помощник
 *
 * @version ${product.version}
 *
 * @copyright 2010, ООО "Два слона", http://dvaslona.ru/
 * @license http://www.gnu.org/licenses/gpl.txt    GPL License 3
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
 * Класс-помощник
 *
 * Класс содержит вспомогательный функционал
 *
 * @package GoodsCatalog
 */
class GoodsCatalog_Helper
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
     * @return GoodsCatalog_Helper
     */
    public function __construct(GoodsCatalog $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * Метод возвращает имя для временного файла в области, доступной для безопасного чтения
     * и записи.
     *
     * @return string
     *
     * @since 1.00
     */
    public function getTempFileName()
    {
        return $this->plugin->getDataDir() . 'tmp_upload.bin';
    }

    /**
     * Возвращает массив данных для шаблона.
     *
     * Массив предварительно наполняется часто используемыми переменными.
     *
     * @return array
     *
     * @since 1.00
     */
    public function prepareTmplData()
    {
        $data = array();
        $data['this'] = $this->plugin;
        $data['page'] = Eresus_Kernel::app()->getPage();
        $data['Eresus'] = Eresus_CMS::getLegacyKernel();
        return $data;
    }
}

