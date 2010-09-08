<?php
/**
 * Каталог товаров
 *
 * Модуль позволяет создать на сайте простой каталог товаров
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
	public $kernel = '2.14b';

	/**
	 * Название плагина
	 * @var string
	 */
	public $title = 'Каталог товаров';

	/**
	 * Опиание плагина
	 * @var string
	 */
	public $description = 'Простой каталог товаров';

	/**
	 * Тип плагина
	 * @var string
	 */
	public $type = 'client,admin,content';

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
		// Вертикальный отступ от края в пикселах
		'logoVPadding' => 10,
		// Горизонтальный отступ от края в пикселах
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
	 * Объект-помщник
	 *
	 * @var GoodsCatalogHelper
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

		/* Настраиваем автозагрузку классов */
		set_include_path(get_include_path() . PATH_SEPARATOR . $this->dirCode);
		EresusClassAutoloader::add($this->dirCode . 'autoload.php');

		$this->listenEvents('adminOnMenuRender');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает URL директории файлов плагина
	 *
	 * @return string
	 *
	 * @since 1.00
	 */
	public function getCodeURL()
	{
		return $this->urlCode;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает путь к директории данных плагина
	 *
	 * @return string
	 *
	 * @since 1.00
	 */
	public function getDataDir()
	{
		return $this->dirData;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает URL директории данных плагина
	 *
	 * @return string
	 *
	 * @since 1.00
	 */
	public function getDataURL()
	{
		return $this->urlData;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Действия при инсталляции
	 *
	 * @return void
	 *
	 * @see main/core/Plugin::install()
	 * @since 1.00
	 */
	public function install()
	{
		global $Eresus;

		parent::install();

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
			`cost` float NOT NULL default 0 COMMENT 'Цена',
			`ext` varchar(4) NOT NULL default '' COMMENT 'Расширение файла основной фотографии',
			`special` bool NOT NULL default 0 COMMENT 'Спецпредложение',
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
			`goods` int(10) unsigned default 0 COMMENT 'Привязка к товару',
			`ext` varchar(4) NOT NULL default '' COMMENT 'Расширение файла',
			PRIMARY KEY  (`id`),
			KEY `admin_list` (`goods`, `position`),
			KEY `client_list` (`active`, `goods`, `position`)
		";
		$this->dbCreateTable($sql, 'photos');

		/* Создаём директории данных */
		$this->mkdir('goods');
		$this->mkdir('brands');

		/* Создаём директорию кэша */
		$umask = umask(0000);
		@mkdir($Eresus->fdata . 'cache', 0777);
		umask($umask);

	}
	//-----------------------------------------------------------------------------

	/**
	 * Действия при удалении плагина
	 *
	 * @return void
	 *
	 * @see main/core/Plugin::uninstall()
	 * @since 1.00
	 */
	public function uninstall()
	{
		/* Удаляем директории данных */
		$this->rmdir();

		parent::uninstall();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Диалог настроек
	 *
	 * @return string
	 *
	 * @since 1.00
	 */
	public function settings()
	{
		global $page;

		$page->linkStyles($this->urlCode . 'admin.css');
		$this->getHelper()->linkJQuery();

		// Данные для подстановки в шаблон
		$data = $this->getHelper()->prepareTmplData();
		$data['logoExists'] = FS::isFile($this->getLogoFileName());

		// Создаём экземпляр шаблона
		$tmpl = $this->getHelper()->getAdminTemplate('settings.html');

		// Компилируем шаблон и данные
		$html = $tmpl->compile($data);

		return $html;
	}
	//-----------------------------------------------------------------------------

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
		$this->uploadLogo();
	}
	//-----------------------------------------------------------------------------

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
			ErrorMessage('Логотип должен быть в формате PNG. Загруженный файл имеет формат "' .
				$info['mime'] . '"');
			return;
		}

		rename($tmpFile, $this->getLogoFileName());

	}
	//-----------------------------------------------------------------------------

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
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает объект-помощник
	 *
	 * @return GoodsCatalogHelper
	 *
	 * @since 1.00
	 */
	public function getHelper()
	{
		if (!$this->helper)
		{
			$this->helper = new GoodsCatalogHelper($this);
		}
		return $this->helper;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Добавляет пункта "Бренды" в меню "Расширения"
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
				'access'  => EDITOR,
				'link'  => $this->name . '&ref=brands',
				'caption'  => 'Бренды',
				'hint'  => 'Управление брендами'
			);
			$GLOBALS['page']->addMenuItem($this->title, $menuItem);
		}
	}
	//-----------------------------------------------------------------------------

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
				'Вы можете включить его в <a href="admin.php?mod=plgmgr&id=' . $this->name .
				'">настройках</a>.');
		}

		$ui = new GoodsCatalogBrandsAdminUI($this);

		return $ui->getHTML();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Формирование HTML-кода АИ
	 *
	 * @return string  HTML
	 *
	 * @since 1.00
	 */
	public function adminRenderContent()
	{
		$ui = new GoodsCatalogGoodsAdminUI($this);

		return $ui->getHTML();
	}
	//-----------------------------------------------------------------------------

}
