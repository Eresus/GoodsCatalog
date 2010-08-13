<?php
/**
 * Каталог товаров
 *
 * Абстрактная реализация паттерна ActiveRecord
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
 * Абстрактная реализация паттерна ActiveRecord
 *
 * @package GoodsCatalog
 */
abstract class GoodsCatalogAbstractActiveRecord
{
	/**
	 * Объект плагина
	 *
	 * @var GoodsCatalog
	 */
	private $plugin;

	/**
	 * Свойства полей
	 *
	 * @var array
	 */
	private $attrs;

	/**
	 * Значения полей
	 *
	 * @var array
	 */
	private $rawData = array();

	/**
	 * Признак новой записи
	 *
	 * @var bool
	 */
	private $isNew = true;

	/**
	 * Конструктор
	 *
	 * @param GoodsCatalog $plugin
	 *
	 * @return GoodsCatalogAbstractActiveRecord
	 */
	public function __construct(GoodsCatalog $plugin)
	{
		$this->plugin = $plugin;
		$this->attrs = $this->getFieldAttrs();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Метод должен возвращать имя таблицы БД
	 *
	 * @return string  Имя таблицы БД
	 *
	 * @since 1.00
	 */
	abstract protected function getTableName();
	//-----------------------------------------------------------------------------

	/**
	 * Метод должен возвращать список полей записи и их атрибуты
	 *
	 * Результат должен быть ассоциативным массивом, где ключами выступают имена полей, а значениями
	 * массивы атрибутов этих полей. Возможны следующие атрибуты:
	 *
	 * - type - Тип поля: string, int, float, bool
	 * - pattern - PCRE для проверки значения
	 * - maxlength - Для строковых полей, максимальное количество символов
	 *
	 * @return array
	 *
	 * @since 1.00
	 */
	abstract protected function getFieldAttrs();
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает значение поля
	 *
	 * @param string $key  Имя поля
	 *
	 * @return mixed  Значение поля
	 *
	 * @throws EresusPropertyNotExistsException
	 * @since 1.00
	 */
	public function __get($key)
	{
		if (!isset($this->attrs[$key]))
		{
			throw new EresusPropertyNotExistsException($key, get_class($this));
		}

		if (isset($this->rawData[$key]))
		{
			return $this->rawData[$key];
		}

		return null;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Задаёт значение поля
	 *
	 * @param string $key    Имя поля
	 * @param mixed  $value  Значение поля
	 *
	 * @return void
	 *
	 * @throws EresusPropertyNotExistsException
	 * @since 1.00
	 */
	public function __set($key, $value)
	{
		if (!isset($this->attrs[$key]))
		{
			throw new EresusPropertyNotExistsException($key, get_class($this));
		}

		$this->rawData[$key] = $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает TRUE если эта запись ещё не добавлена в БД
	 *
	 * @return bool
	 *
	 * @since 1.00
	 */
	public function isNew()
	{
		return $this->isNew;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Сохраняет изменения в БД
	 *
	 * @return void
	 *
	 * @since 1.00
	 */
	public function save()
	{
		if ($this->isNew())
		{
			$this->plugin->dbInsert($this->getTableName(), $this->rawData);
		}
	}
	//-----------------------------------------------------------------------------
}
