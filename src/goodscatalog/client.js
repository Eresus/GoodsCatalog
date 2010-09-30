/**
 * Каталог товаров
 *
 * Клиентские скрипты КИ
 *
 * @version ${product.version}
 *
 * @copyright 2010, ООО "Два слона", http://dvaslona.ru/
 * @license http://www.gnu.org/licenses/gpl.txt  GPL License 3
 * @author Михаил Красильников <mk@3wstyle.ru>
 *
 * Данная программа является свободным программным обеспечением. Вы
 * вправе распространять ее и/или модифицировать в соответствии с
 * условиями версии 3 либо по вашему выбору с условиями более поздней
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

jQuery('a[href$="#catalog-popup"]').live('click', function (e)
{
	e.stopPropagation();
	e.preventDefault();
	
	jQuery('<img src="' + e.currentTarget.href + '" alt="" />').dialog({
		width: 'auto',
		draggable: false,
		modal: true,
		closeText: 'Закрыть',
		resizable: false,
		close: function(event)
		{ 
			jQuery(event.target).remove().closest('div.ui-dialog').remove();
		},
		/**
		 * Исправляем позиционирование диалога. Размещаем его по центру окна
		 * 
		 * После выполнения http://bugs.eresus.ru/view.php?id=496 можно переделать с использованием
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
