<?php
/**
 * ������� �������
 *
 * ������ ��������� ������� �� ����� ������� ������� �������
 *
 * @version ${product.version}
 *
 * @copyright 2010, ��� "��� �����", http://dvaslona.ru/
 * @license http://www.gnu.org/licenses/gpl.txt	GPL License 3
 * @author ������ ������������ <mk@3wstyle.ru>
 *
 * ������ ��������� �������� ��������� ����������� ������������. ��
 * ������ �������������� �� �/��� �������������� � ������������ �
 * ��������� ������ 3 ���� (�� ������ ������) � ��������� ����� �������
 * ������ ����������� ������������ �������� GNU, �������������� Free
 * Software Foundation.
 *
 * �� �������������� ��� ��������� � ������� �� ��, ��� ��� ����� ���
 * ��������, ������ �� ������������� �� ��� ������� ��������, � ���
 * ����� �������� ��������� ��������� ��� ������� � ����������� ���
 * ������������� � ���������� �����. ��� ��������� ����� ���������
 * ���������� ������������ �� ����������� ������������ ��������� GNU.
 *
 * �� ������ ���� �������� ����� ����������� ������������ ��������
 * GNU � ���� ����������. ���� �� �� �� ��������, �������� �������� ��
 * <http://www.gnu.org/licenses/>
 *
 * @package GoodsCatalog
 *
 * $Id$
 */

/**
 * �������� ����� �������
 *
 * @package GoodsCatalog
 */
class GoodsCatalog extends ContentPlugin
{
	/**
	 * ������ �������
	 * @var string
	 */
	public $version = '${product.version}';

	/**
	 * ��������� ������ ����
	 * @var string
	 */
	public $kernel = '2.16';

	/**
	 * �������� �������
	 * @var string
	 */
	public $title = '������� �������';

	/**
	 * �������� �������
	 * @var string
	 */
	public $description = '������� ������� �������';

	/**
	 * ��������� �������
	 *
	 * @var array
	 */
	public $settings = array(
		// ���-�� ������� �� ��������
		'goodsPerPage' => 10,

		/* ������� */
		// ������������ �������
		'logoEnabled' => false,
		// ��������� ��������
		'logoPosition' => 'BL', // ��������: TL, TR, BL, Br. T - ����, B - ���, L - ����, R - �����.
		// ������������ ������ �� ���� � ��������
		'logoVPadding' => 10,
		// �������������� ������ �� ���� � ��������
		'logoHPadding' => 10,

		// ������������ �������� ����������
		'mainPhotoEnabled' => false,

		// ������������ �������������� ����������
		'extPhotosEnabled' => false,

		/* ���������� */
		'photoMaxWidth' => 800,
		'photoMaxHeight' => 600,

		/* ��������� */
		'thumbWidth' => 200,
		'thumbHeight' => 150,

		// ������������ ������
		'brandsEnabled' => false,

		/* ������� ������ */
		'brandLogoMaxWidth' => 300,
		'brandLogoMaxHeight' => 300,

		// ������������ ���������������
		'specialsEnabled' => false
	);

	/**
	 * ������-��������
	 *
	 * @var GoodsCatalogHelper
	 * @since 1.00
	 */
	private $helper;

	/**
	 * �����������
	 *
	 * @return GoodsCatalog
	 *
	 * @since 1.00
	 */
	public function __construct()
	{
		parent::__construct();

		// ����������� ������������ �������
		EresusClassAutoloader::add($this->dirCode . 'autoload.php');

		$this->listenEvents('adminOnMenuRender');
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� URL ���������� ������ �������
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
	 * ���������� ���� � ���������� ������ �������
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
	 * ���������� URL ���������� ������ �������
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
	 * �������� ��� �����������
	 *
	 * @return void
	 *
	 * @see main/core/Plugin::install()
	 * @since 1.00
	 */
	public function install()
	{
		parent::install();

		try
		{
			/*
			 * ������� �������
			 */
			$sql = "
				`id` int(10) unsigned NOT NULL auto_increment COMMENT '�������������',
				`section` int(10) unsigned NOT NULL COMMENT '�������� � �������',
				`active` bool NOT NULL default 0 COMMENT '����������',
				`position` int(10) unsigned NOT NULL default '0' COMMENT '���������� �����',
				`article` varchar(255) NOT NULL default '' COMMENT '�������',
				`title` varchar(255) NOT NULL default '' COMMENT '��������',
				`about` text NOT NULL default '' COMMENT '������� ��������',
				`description` longtext NOT NULL default '' COMMENT '��������',
				`cost` double NOT NULL default 0 COMMENT '����',
				`ext` varchar(4) NOT NULL default '' COMMENT '���������� ����� �������� ����������',
				`special` bool NOT NULL default 0 COMMENT '���������������',
				`brand` int(10) unsigned default NULL COMMENT '�������� � ������',
				PRIMARY KEY  (`id`),
				KEY `admin_list` (`section`, `position`),
				KEY `client_list` (`active`, `section`, `position`),
				KEY `admin_special` (`special`),
				KEY `client_special` (`active`, `special`)
			";
			$this->dbCreateTable($sql, 'goods');

			/*
			 * ������� �������
			 */
			$sql = "
				`id` int(10) unsigned NOT NULL auto_increment COMMENT '�������������',
				`active` bool NOT NULL default 0 COMMENT '����������',
				`title` varchar(255) NOT NULL default '' COMMENT '��������',
				`description` longtext NOT NULL default '' COMMENT '��������',
				`ext` varchar(4) NOT NULL default '' COMMENT '���������� ����� ��������',
				PRIMARY KEY  (`id`),
				KEY `admin_list` (`title`),
				KEY `client_list` (`active`, `title`)
			";
			$this->dbCreateTable($sql, 'brands');

			/*
			 * ������� �������������� ����������
			 */
			$sql = "
				`id` int(10) unsigned NOT NULL auto_increment COMMENT '�������������',
				`active` bool NOT NULL default 0 COMMENT '����������',
				`position` int(10) unsigned NOT NULL default '0' COMMENT '���������� �����',
				`good` int(10) unsigned default 0 COMMENT '�������� � ������',
				`ext` varchar(4) NOT NULL default '' COMMENT '���������� �����',
				PRIMARY KEY  (`id`),
				KEY `admin_list` (`good`, `position`),
				KEY `client_list` (`active`, `good`, `position`)
			";
			$this->dbCreateTable($sql, 'photos');
		}
		catch (Exception $e)
		{
			$this->uninstall();
			throw new EresusRuntimeException('Fail to create DB tables',
				'�� ������� ������� ������� � ���� ������. ��������� ���������� �������� � �������.', $e);
		}

		/* ������ ���������� ������ */
		$this->mkdir('goods');
		$this->mkdir('brands');

		$ts = GoodsCatalogTemplateService::getInstance();

		try
		{
			$ts->installTemplates($this->dirCode . 'distrib/templates', $this->name);
		}
		catch (Exception $e)
		{
			$this->uninstall();
			throw new EresusRuntimeException('Fail to install templates',
				'�� ������� ���������� ������� �������. ��������� ���������� �������� � �������.', $e);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * �������� ��� �������� �������
	 *
	 * @return void
	 *
	 * @see main/core/Plugin::uninstall()
	 * @since 1.00
	 */
	public function uninstall()
	{
		$ts = GoodsCatalogTemplateService::getInstance();

		try
		{
			$ts->uninstall($this->name);
		}
		catch (Exception $e)
		{
			throw new EresusRuntimeException('Fail to uninstall templates',
				'�� ������� ������� ������� �������. ��������� ���������� �������� � �������.', $e);
		}

		/* ������� ���������� ������ */
		$this->rmdir();

		parent::uninstall();
	}
	//-----------------------------------------------------------------------------

	/**
	 * ������ ��������
	 *
	 * @return string
	 *
	 * @since 1.00
	 */
	public function settings()
	{
		global $page;

		$page->linkStyles($this->urlCode . 'admin.css');

		// ������ ��� ����������� � ������
		$data = $this->getHelper()->prepareTmplData();
		$data['logoExists'] = FS::isFile($this->getLogoFileName());

		// ������ ��������� �������
		//$tmpl = $this->getHelper()->getAdminTemplate('settings.html');
		$form = new EresusForm('ext/' . $this->name . '/templates/settings.html', LOCALE_CHARSET);

		foreach ($data as $key => $value)
		{
			$form->setValue($key, $value);
		}

		$ts = GoodsCatalogTemplateService::getInstance();

		$this->settings['tmplList'] = $ts->getContents('goods-list.html', $this->name);
		$this->settings['tmplItem'] = $ts->getContents('goods-item.html', $this->name);

		// ����������� ������ � ������
		//$html = $tmpl->compile($data);
		$html = $form->compile();

		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * �������������� �������� ��� ���������� ��������
	 *
	 * @return void
	 *
	 * @see main/core/Plugin::onSettingsUpdate()
	 * @since 1.00
	 */
	public function onSettingsUpdate()
	{
		$ts = GoodsCatalogTemplateService::getInstance();

		$ts->setContents(arg('tmplList'), 'goods-list.html', $this->name);
		$ts->setContents(arg('tmplItem'), 'goods-item.html', $this->name);

		$this->uploadLogo();
	}
	//-----------------------------------------------------------------------------

	/**
	 * ��������� ���� ��������
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
			ErrorMessage('������� ������ ���� � ������� PNG. ����������� ���� ����� ������ "' .
				$info['mime'] . '"');
			return;
		}

		rename($tmpFile, $this->getLogoFileName());

	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� ��� ����� ��������
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
	 * ���������� ������-��������
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
	 * (non-PHPdoc)
	 * @see main/core/Plugin::mkdir()
	 * @since 1.00
	 */
	public function mkdir($name = '')
	{
		return parent::mkdir($name);
	}
	//-----------------------------------------------------------------------------

	/**
	 * ��������� ����� "������" � ���� "����������"
	 *
	 * @return void
	 *
	 * @since 1.00
	 */
	public function adminOnMenuRender()
	{
		/* ��������� ����� ������ ���� �������� ��������������� ����� */
		if ($this->settings['brandsEnabled'])
		{
			$menuItem = array(
				'access'  => EDITOR,
				'link'  => $this->name . '&ref=brands',
				'caption'  => '������',
				'hint'  => '���������� ��������'
			);
			$GLOBALS['page']->addMenuItem($this->title, $menuItem);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� ������� �������������� �����������
	 *
	 * @return string  HTML
	 *
	 * @since 1.00
	 */
	public function adminRender()
	{
		if ($this->settings['brandsEnabled'] == false)
		{
			return ErrorBox('���������� ���������� �������� ��������. ' .
				'�� ������ �������� ��� � <a href="admin.php?mod=plgmgr&id=' . $this->name .
				'">����������</a>.');
		}

		$ui = new GoodsCatalogBrandsAdminUI($this);

		return $ui->getHTML();
	}
	//-----------------------------------------------------------------------------

	/**
	 * ������������ HTML-���� ��
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

	/**
	 * ������������ HTML-���� ��
	 *
	 * @return string  HTML
	 *
	 * @since 1.00
	 */
	public function clientRenderContent()
	{
		$ui = new GoodsCatalogGoodsClientUI($this);

		return $ui->getHTML();
	}
	//-----------------------------------------------------------------------------

}
