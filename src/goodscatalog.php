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
	public $kernel = '2.12';

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
	 * �������� ��� �����������
	 *
	 * @return void
	 * @see main/core/Plugin::install()
	 */
	public function install()
	{
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
	}
	//-----------------------------------------------------------------------------
}
