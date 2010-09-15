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

jQuery('a[href$="#catalog-popup"]').live('click', function (e)
{
	e.stopPropagation();
	e.preventDefault();
	
	var popup = jQuery('<img src="' + e.currentTarget.href + '" alt="" />').dialog({
		width: 'auto',
		modal: true,
		closeText: '�������',
		resizable: false,
		close: function(event) { 
			jQuery(event.target).remove().closest('div.ui-dialog').remove();
		}
	});
});
