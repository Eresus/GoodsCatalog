/**
 * ������� �������
 *
 * ���������� ������� ��
 *
 * @version ${product.version}
 *
 * @copyright 2010, ��� "��� �����", http://dvaslona.ru/
 * @license http://www.gnu.org/licenses/gpl.txt  GPL License 3
 * @author ������ ������������ <mk@3wstyle.ru>
 *
 * ������ ��������� �������� ��������� ����������� ������������. ��
 * ������ �������������� �� �/��� �������������� � ������������ �
 * ��������� ������ 3 ���� �� ������ ������ � ��������� ����� �������
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
 * ������ ���������� ��������
 */
var GoodsCatalog = {
	// ������� ��������� ������ � �������
	dialogDataChanged: false
};

/* ������� ������������� �� �������� �������� */
jQuery('#content .goods-list-item a.delete').live('click', function (e)
{
	return confirm("������������� �������� ������?");
});

jQuery('#content .photo-list-item a.delete').live('click', function (e)
{
	return confirm("������������� �������� ����������?");
});

jQuery('#content .brand-list-item a.delete').live('click', function (e)
{
	return confirm("������������� �������� ������?");
});


/**
 * ������������ ��������� �� ������� "�������� �������� ������"
 */
jQuery('#catalogEdit-main :input').live('change', function ()
{
	GoodsCatalog.dialogDataChanged = true;
});

jQuery(document).ready(function () 
{
	jQuery('#catalogEdit-btn-images a').click(function (e) 
	{
		if (GoodsCatalog.dialogDataChanged)
		{
			alert('�� ������� "�������� ��������" ���� ������������ ���������. ��������� �� ������ ��� ������� � �������������� �����������.');
			jQuery(e).stopPropagation().preventDefault();
		}
	});
});