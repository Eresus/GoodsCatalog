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
	
	jQuery('<img src="' + e.currentTarget.href + '" alt="" />').dialog({
		width: 'auto',
		draggable: false,
		modal: true,
		closeText: '�������',
		resizable: false,
		close: function(event)
		{ 
			jQuery(event.target).remove().closest('div.ui-dialog').remove();
		},
		/**
		 * ���������� ���������������� �������. ��������� ��� �� ������ ����
		 * 
		 * ����� ���������� http://bugs.eresus.ru/view.php?id=496 ����� ���������� � ��������������
		 * position()
		 */
		open: function(event)
		{
			jQuery(event.target).closest('div.ui-dialog').hide();
			setTimeout(function ()
			{
				var dlg = jQuery(event.target).closest('div.ui-dialog').eq(0);
				var dlgWidth = dlg.width();
				var dlgHeight = dlg.height();
				
				var body = jQuery('body').eq(0);
				var bodyWidth = body.width();
				var bodyHeight = body.height();
				
				var left = Math.round((bodyWidth - dlgWidth) / 2, 0);
				var top = Math.round((bodyHeight - dlgHeight) / 2, 0);
				dlg.css('left', left + 'px').css('top', (jQuery(document).scrollTop() + top) + 'px').show();
			}, 1);
		}
	});
});
