<?php
/**
 * Каталог товаров
 *
 * Модуль позволяет создать на сайте простой каталог товаров
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
 * Основной класс плагина
 *
 * @package GoodsCatalog
 */
class GoodsCatalog extends ContentPlugin
{
    /**
     * Версия плагина
     * @var string
     */
    public $version = '${product.version}';

    /**
     * Требуемая версия ядра
     * @var string
     */
    public $kernel = '3.01a';

    /**
     * Название плагина
     * @var string
     */
    public $title = 'Каталог товаров';

    /**
     * Описание плагина
     * @var string
     */
    public $description = 'Простой каталог товаров';

    /**
     * Настройки плагина
     *
     * @var array
     */
    public $settings = array(
        // Кол-во товаров на странице
        'goodsPerPage' => 10,

        /* Логотип */
        // Использовать логотип
        'logoEnabled' => false,
        // Положение логотипа
        'logoPosition' => 'BL', // Значения: TL, TR, BL, Br. T - верх, B - низ, L - лево, R - право.
        // Вертикальный отступ от края в пикселях
        'logoVPadding' => 10,
        // Горизонтальный отступ от края в пикселях
        'logoHPadding' => 10,

        // Использовать основную фотографию
        'mainPhotoEnabled' => false,

        // Использовать дополнительные фотографии
        'extPhotosEnabled' => false,

        /* Фотографии */
        'photoMaxWidth' => 800,
        'photoMaxHeight' => 600,

        /* Миниатюры */
        'thumbWidth' => 200,
        'thumbHeight' => 150,

        // Использовать бренды
        'brandsEnabled' => false,

        /* Логотип бренда */
        'brandLogoMaxWidth' => 300,
        'brandLogoMaxHeight' => 300,

        // Использовать Спецпредложения
        'specialsEnabled' => false
    );

    /**
     * Объект-помощник
     *
     * @var GoodsCatalog_Helper
     * @since 1.00
     */
    private $helper;

    /**
     * Конструктор
     *
     * @return GoodsCatalog
     *
     * @since 1.00
     */
    public function __construct()
    {
        parent::__construct();

        Eresus_Kernel::app()->getEventDispatcher()
            ->addListener('cms.admin.start', array($this, 'adminOnMenuRender'));
    }

    /**
     * Действия при инсталляции
     *
     * @throws RuntimeException
     *
     * @return void
     *
     * @see Plugin::install()
     * @since 1.00
     */
    public function install()
    {
        parent::install();

        try
        {
            /*
             * Таблица товаров
             */
            $sql = "
				`id` int(10) unsigned NOT NULL auto_increment COMMENT 'Идентификатор',
				`section` int(10) unsigned NOT NULL COMMENT 'Привязка к разделу',
				`active` bool NOT NULL default 0 COMMENT 'Активность',
				`position` int(10) unsigned NOT NULL default '0' COMMENT 'Порядковый номер',
				`article` varchar(255) NOT NULL default '' COMMENT 'Артикул',
				`title` varchar(255) NOT NULL default '' COMMENT 'Название',
				`about` text NOT NULL default '' COMMENT 'Краткое описание',
				`description` longtext NOT NULL default '' COMMENT 'Описание',
				`cost` double NOT NULL default 0 COMMENT 'Цена',
				`ext` varchar(4) NOT NULL default '' COMMENT 'Расширение файла основной фотографии',
				`special` bool NOT NULL default 0 COMMENT 'Спец. предложение',
				`brand` int(10) unsigned default NULL COMMENT 'Привязка к бренду',
				PRIMARY KEY  (`id`),
				KEY `admin_list` (`section`, `position`),
				KEY `client_list` (`active`, `section`, `position`),
				KEY `admin_special` (`special`),
				KEY `client_special` (`active`, `special`)
			";
            $this->dbCreateTable($sql, 'goods');

            /*
             * Таблица брендов
             */
            $sql = "
				`id` int(10) unsigned NOT NULL auto_increment COMMENT 'Идентификатор',
				`active` bool NOT NULL default 0 COMMENT 'Активность',
				`title` varchar(255) NOT NULL default '' COMMENT 'Название',
				`description` longtext NOT NULL default '' COMMENT 'Описание',
				`ext` varchar(4) NOT NULL default '' COMMENT 'Расширение файла логотипа',
				PRIMARY KEY  (`id`),
				KEY `admin_list` (`title`),
				KEY `client_list` (`active`, `title`)
			";
            $this->dbCreateTable($sql, 'brands');

            /*
             * Таблица дополнительных фотографий
             */
            $sql = "
				`id` int(10) unsigned NOT NULL auto_increment COMMENT 'Идентификатор',
				`active` bool NOT NULL default 0 COMMENT 'Активность',
				`position` int(10) unsigned NOT NULL default '0' COMMENT 'Порядковый номер',
				`good` int(10) unsigned default 0 COMMENT 'Привязка к товару',
				`ext` varchar(4) NOT NULL default '' COMMENT 'Расширение файла',
				PRIMARY KEY  (`id`),
				KEY `admin_list` (`good`, `position`),
				KEY `client_list` (`active`, `good`, `position`)
			";
            $this->dbCreateTable($sql, 'photos');
        }
        catch (Exception $e)
        {
            $this->uninstall();
            throw new RuntimeException('Fail to create DB tables', 0, $e);
        }

        /* Создаём директории данных */
        $this->mkdir('goods');
        $this->mkdir('brands');
    }

    /**
     * Действия при удалении плагина
     *
     * @throws EresusRuntimeException
     *
     * @return void
     *
     * @see Plugin::uninstall()
     * @since 1.00
     */
    public function uninstall()
    {
        /* Удаляем директории данных */
        $this->rmdir();

        parent::uninstall();
    }

    /**
     * Диалог настроек
     *
     * @return string
     *
     * @since 1.00
     */
    public function settings()
    {
        /** @var TAdminUI $page */
        $page = Eresus_Kernel::app()->getPage();

        $page->linkStyles($this->urlCode . 'admin.css');

        // Данные для подстановки в шаблон
        $data = $this->getHelper()->prepareTmplData();
        $data['logoExists'] = is_file($this->getLogoFileName());

        // Создаём экземпляр шаблона
        //$tmpl = $this->getHelper()->getAdminTemplate('settings.html');
        $form = new EresusForm('ext/' . $this->getName() . '/templates/admin/settings.html');

        foreach ($data as $key => $value)
        {
            $form->setValue($key, $value);
        }

        $this->settings['tmplList'] = $this->templates()->clientRead('goods-list.html');
        $this->settings['tmplItem'] = $this->templates()->clientRead('goods-item.html');

        // Компилируем шаблон и данные
        //$html = $tmpl->compile($data);
        $html = $form->compile();

        return $html;
    }

    /**
     * Дополнительные действия при сохранении настроек
     *
     * @return void
     *
     * @see main/core/Plugin::onSettingsUpdate()
     * @since 1.00
     */
    public function onSettingsUpdate()
    {
        $this->templates()->clientWrite('goods-list.html', arg('tmplList'));
        $this->templates()->clientWrite('goods-item.html', arg('tmplItem'));

        $this->uploadLogo();
    }

    /**
     * Загружает файл логотипа
     *
     * @return void
     *
     * @since 1.00
     */
    private function uploadLogo()
    {
        $tmpFile = $this->getHelper()->getTempFileName();
        if (!upload('logoImage', $tmpFile))
        {
            return;
        }

        $info = getimagesize($tmpFile);
        if ($info['mime'] != 'image/png')
        {
            Eresus_Kernel::app()->getPage()->addErrorMessage(
                'Логотип должен быть в формате PNG. Загруженный файл имеет формат "' .
            $info['mime'] . '"');
            return;
        }

        rename($tmpFile, $this->getLogoFileName());

    }

    /**
     * Возвращает имя файла логотипа
     *
     * @return string
     *
     * @since 1.00
     */
    private function getLogoFileName()
    {
        return $this->dirData . 'logo.png';
    }

    /**
     * Возвращает объект-помощник
     *
     * @return GoodsCatalog_Helper
     *
     * @since 1.00
     */
    public function getHelper()
    {
        if (!$this->helper)
        {
            $this->helper = new GoodsCatalog_Helper($this);
        }
        return $this->helper;
    }

    /**
     * @see Plugin::mkdir()
     * @since 1.00
     */
    public function mkdir($name = '')
    {
        return parent::mkdir($name);
    }

    /**
     * Добавляет пункт "Бренды" в меню "Расширения"
     *
     * @return void
     *
     * @since 1.00
     */
    public function adminOnMenuRender()
    {
        /* Добавляем пункт только если включена соответствующая опция */
        if ($this->settings['brandsEnabled'])
        {
            $menuItem = array(
                'access' => EDITOR,
                'link' => $this->getName() . '&ref=brands',
                'caption' => 'Бренды',
                'hint' => 'Управление брендами'
            );
            /** @var TAdminUI $page */
            $page = Eresus_Kernel::app()->getPage();
            $page->addMenuItem($this->title, $menuItem);
        }
    }

    /**
     * Возвращает контент дополнительных интерфейсов
     *
     * @return string  HTML
     *
     * @since 1.00
     */
    public function adminRender()
    {
        if ($this->settings['brandsEnabled'] == false)
        {
            return ErrorBox('Функционал управления брендами отключен. ' .
            'Вы можете включить его в <a href="admin.php?mod=plgmgr&id=' . $this->getName() .
            '">настройках</a>.');
        }

        $ui = new GoodsCatalog_BrandsAdminUI($this);

        return $ui->getHTML();
    }

    /**
     * Формирование HTML-кода АИ
     *
     * @return string  HTML
     *
     * @since 1.00
     */
    public function adminRenderContent()
    {
        $ui = new GoodsCatalog_GoodsAdminUI($this);

        return $ui->getHTML();
    }

    /**
     * Формирование HTML-кода КИ
     *
     * @return string  HTML
     *
     * @since 1.00
     */
    public function clientRenderContent()
    {
        $ui = new GoodsCatalog_GoodsClientUI($this);

        return $ui->getHTML();
    }

    /**
     * Удаляет товары при удалении раздела
     *
     * @param int $id
     *
     * @return void
     *
     * @since 2.00
     */
    public function onSectionDelete($id)
    {
        set_time_limit(0);
        $goods = GoodsCatalog_Good::find($id);
        foreach ($goods as $good)
        {
            $good->delete();
        }
    }
}

