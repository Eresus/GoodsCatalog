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
 * @author ������ ������� <ghost@dvaslona.ru>
 * @author Timofey Finogenov
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
	public $kernel = '2.13';

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
		global $Eresus;

		parent::__construct();

		if (!Core::getValue('core.template.templateDir'))
		{
			Core::setValue('core.template.templateDir', $Eresus->froot);
		}

		if (!Core::getValue('core.template.compileDir'))
		{
			Core::setValue('core.template.compileDir', $Eresus->fdata . 'cache');
		}

		if (!Core::getValue('core.template.charset'))
		{
			Core::setValue('core.template.charset', 'windows-1251');
		}

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
		$tmpl = $this->adminGetTemplate('settings.html');

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
	private function getHelper()
	{
		if (!$this->helper)
		{
			$this->helper = new GoodsCatalogHelper($this);
		}
		return $this->helper;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� ��������� ������� � ��������� ������
	 *
	 * @param string $name  ��� ����� ������� ������������ ���������� �������� �������
	 *
	 * @return Template
	 *
	 * @since 1.00
	 */
	private function adminGetTemplate($name)
	{
		$tmpl = new Template('ext/' . $this->name . '/templates/' . $name);
		return $tmpl;
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
		switch (true)
		{
			case arg('action') == 'add':
				$html = $this->adminAddGoodDialog();
			break;

			default:
				$html = $this->adminRenderGoodsList();
			break;
		}

		// �������������� �����
		$GLOBALS['page']->linkStyles($this->urlCode . 'admin.css');

		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ������������ ��������� ������ �������
	 *
	 * @return string  HTML
	 *
	 * @since 1.00
	 */
	private function adminRenderGoodsList()
	{
		// ������ ��� ����������� � ������
		$data = $this->getHelper()->prepareTmplData();
		$data['sectionId'] = arg('section', 'int');

		// ������ ��������� �������
		$tmpl = $this->adminGetTemplate('goods-list.html');

		// ����������� ������ � ������
		$html = $tmpl->compile($data);

		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� ������ ���������� ������
	 *
	 * @return string  HTML
	 *
	 * @since 1.00
	 */
	private function adminAddGoodDialog()
	{
		// ������ ��� ����������� � ������
		$data = $this->getHelper()->prepareTmplData();
		$data['sectionId'] = arg('section', 'int');

		// ������ ��������� �������
		$tmpl = $this->adminGetTemplate('goods-add.html');

		// ����������� ������ � ������
		$html = $tmpl->compile($data);

		return $html;
	}
	//-----------------------------------------------------------------------------
}




/**
 * �����-��������
 *
 * ����� �������� ��������������� ����������
 *
 * @package GoodsCatalog
 */
class GoodsCatalogHelper
{
	/**
	 * ������ �������
	 *
	 * @var GoodsCatalog
	 */
	private $plugin;

	/**
	 * �����������
	 *
	 * @param GoodsCatalog $plugin
	 *
	 * @return GoodsCatalogHelper
	 */
	public function __construct(GoodsCatalog $plugin)
	{
		$this->plugin = $plugin;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ����� ���������� ��� ��� ���������� ����� � �������, ��������� ��� ����������� ������
	 * � ������.
	 *
	 * @return string
	 *
	 * @since 1.00
	 */
	public function getTempFileName()
	{
		return $this->plugin->dirData . 'tmp_upload.bin';
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� ���������� jQuery
	 *
	 * @return void
	 *
	 * @since 1.00
	 */
	public function linkJQuery()
	{
		global $Eresus, $page;

		$page->linkScripts($Eresus->root . 'core/jquery/jquery.min.js');
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� ������ ������ ��� �������.
	 *
	 * ������ �������������� ����������� ������������������ �����������.
	 *
	 * @return array
	 *
	 * @since 1.00
	 */
	public function prepareTmplData()
	{
		$data = array();
		$data['this'] = $this->plugin;
		$data['page'] = $GLOBALS['page'];
		$data['Eresus'] = $GLOBALS['Eresus'];
		return $data;
	}
	//-----------------------------------------------------------------------------
}