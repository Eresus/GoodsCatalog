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
	public $kernel = '2.14b';

	/**
	 * �������� �������
	 * @var string
	 */
	public $title = '������� �������';

	/**
	 * ������� �������
	 * @var string
	 */
	public $description = '������� ������� �������';

	/**
	 * ��� �������
	 * @var string
	 */
	public $type = 'client,admin,content';

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
	 * ������-�������
	 *
	 * @var GoodsCatalogHelper
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

		/* ����������� ������������ ������� */
		set_include_path(get_include_path() . PATH_SEPARATOR . $this->dirCode);
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
		global $Eresus;

		parent::install();

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
			`cost` float NOT NULL default 0 COMMENT '����',
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
			`goods` int(10) unsigned default 0 COMMENT '�������� � ������',
			`ext` varchar(4) NOT NULL default '' COMMENT '���������� �����',
			PRIMARY KEY  (`id`),
			KEY `admin_list` (`goods`, `position`),
			KEY `client_list` (`active`, `goods`, `position`)
		";
		$this->dbCreateTable($sql, 'photos');

		/* ������ ���������� ������ */
		$this->mkdir('goods');
		$this->mkdir('brands');

		/* ������ ���������� ���� */
		$umask = umask(0000);
		@mkdir($Eresus->fdata . 'cache', 0777);
		umask($umask);

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
		$this->getHelper()->linkJQuery();

		// ������ ��� ����������� � ������
		$data = $this->getHelper()->prepareTmplData();
		$data['logoExists'] = FS::isFile($this->getLogoFileName());

		// ������ ��������� �������
		$tmpl = $this->getHelper()->getAdminTemplate('settings.html');

		// ����������� ������ � ������
		$html = $tmpl->compile($data);

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
	 * ��������� ������ "������" � ���� "����������"
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

}
