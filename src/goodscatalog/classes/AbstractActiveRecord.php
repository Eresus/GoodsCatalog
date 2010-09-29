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
 *
 * @since 1.00
 */
abstract class GoodsCatalogAbstractActiveRecord
{
	/**
	 * Имя свойства, определяющего владельца
	 * @var string
	 */
	protected $ownerProperty = 'section';

	/**
	 * Объект плагина
	 *
	 * @var GoodsCatalog
	 * @since 1.00
	 */
	private static $plugin;

	/**
	 * Значения полей
	 *
	 * @var array
	 * @since 1.00
	 */
	private $rawData = array();

	/**
	 * Кэш значений свойств
	 *
	 * @var array
	 * @since 1.00
	 */
	private $propertyCache = array();

	/**
	 * Признак новой записи
	 *
	 * @var bool
	 * @since 1.00
	 */
	private $isNew = true;

	/**
	 * Список поддерживаемых форматов
	 * @var array
	 */
	private $supportedFormats = array(
		'image/jpeg',
		'image/jpg',
		'image/pjpeg',
		'image/png',
		'image/gif',
	);

	/**
	 * Описание файла для загрузки
	 *
	 * @var array
	 */
	protected $upload;

	/**
	 * Конструктор
	 *
	 * @param int $id  Идентификатор
	 *
	 * @return GoodsCatalogAbstractActiveRecord
	 *
	 * @since 1.00
	 */
	public function __construct($id = null)
	{
		eresus_log(array(__METHOD__, get_class($this)), LOG_DEBUG, '(%s)', $id);
		if ($id !== null)
		{
			$this->loadById($id);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Метод должен возвращать имя таблицы БД
	 *
	 * @return string
	 *
	 * @since 1.00
	 */
	abstract public function getTableName();
	//-----------------------------------------------------------------------------

	/**
	 * Метод должен возвращать список полей записи и их атрибуты
	 *
	 * Значение должно быть ассоциативным массивом, где ключами выступают имена полей, а значениями
	 * массивы атрибутов этих полей. Возможны следующие атрибуты:
	 *
	 * - type - Тип поля: PDO::PARAM_STR, PDO::PARAM_INT, PDO::PARAM_BOOL
	 * - pattern - PCRE для проверки значения
	 * - maxlength - Для строковых полей, максимальное количество символов
	 *
	 * @return array
	 *
	 * @since 1.00
	 */
	abstract public function getAttrs();
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает полное имя таблицы (плагин_таблица)
	 *
	 * @return string
	 *
	 * @since 1.00
	 */
	public function getDbTable()
	{
		return self::plugin()->name . '_' . $this->getTableName();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает полное имя таблицы (для статических вызовов)
	 *
	 * @param string $className  Имя класса, потомка GoodsCatalogAbstractActiveRecord, для которого
	 *                           надо получить имя таблицы
	 * @return string
	 *
	 * @since 1.00
	 */
	public static function getDbTableStatic($className)
	{
		$stub = new $className();

		if (!($stub instanceof GoodsCatalogAbstractActiveRecord))
		{
			throw new EresusTypeException();
		}

		return $stub->getDbTable();
	}
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
		$getter = 'get' . $key;
		if (method_exists($this, $getter))
		{
			return $this->$getter();
		}

		return $this->getProperty($key);
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
	 * @since 1.00
	 */
	public function __set($key, $value)
	{
		$setter = 'set' . $key;
		if (method_exists($this, $setter))
		{
			$this->$setter($value);
		}
		else
		{
			$this->setProperty($key, $value);
		}
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
		eresus_log(__METHOD__, LOG_DEBUG, '()');

		$db = DB::getHandler();
		if ($this->isNew())
		{
			$q = $db->createInsertQuery();
			$q->insertInto($this->getDbTable());
		}
		else
		{
			$q = $db->createUpdateQuery();
			$q->update($this->getDbTable())
				->where($q->expr->eq('id', $q->bindValue($this->id,null, PDO::PARAM_INT)));
		}

		foreach ($this->attrs as $key => $attrs)
		{
			if (isset($this->rawData[$key]))
			{
				$q->set($key, $q->bindValue($this->rawData[$key], null, $attrs['type']));
			}
		}

		DB::execute($q);

		if ($this->isNew())
		{
			$this->id = $db->lastInsertId();
			$wasNew = true;
		}
		else
		{
			$wasNew = false;
		}

		$this->isNew = false;

		if ($this->upload)
		{
			try
			{
				$this->serveUpload();
			}
			catch (Exception $e)
			{
				if ($wasNew)
				{
					$this->delete();
				}
				throw $e;
			}
		}

	}
	//-----------------------------------------------------------------------------

	/**
	 * Удаляет запись
	 *
	 * @return void
	 *
	 * @since 1.00
	 */
	public function delete()
	{
		eresus_log(__METHOD__, LOG_DEBUG, '()');

		$db = DB::getHandler();
		if (!$this->isNew())
		{
			$q = $db->createDeleteQuery();
			$q->deleteFrom($this->getDbTable())
				->where($q->expr->eq('id', $q->bindValue($this->id,null, PDO::PARAM_INT)));

			DB::execute($q);
		}

		$this->isNew = true;
		$this->rawData = array();
		$this->propertyCache = array();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Перемещает объект вверх по списку
	 *
	 * @return void
	 *
	 * @since 1.00
	 */
	public function moveUp()
	{
		if ($this->position == 0)
		{
			return;
		}

		$q = DB::getHandler()->createSelectQuery();
		$e = $q->expr;
		$q->select('*')->from($this->getDbTable())
			->where($e->lAnd(
				$e->eq($this->ownerProperty,
					$q->bindValue($this->getProperty($this->ownerProperty), null, PDO::PARAM_INT)),
				$e->lt('position', $q->bindValue($this->position, null, PDO::PARAM_INT))
			))
			->orderBy('position', ezcQuerySelect::DESC)
			->limit(1);

		$raw = DB::fetch($q);

		if (!$raw)
		{
			return;
		}

		$class = get_class($this);
		$swap = new $class;
		$swap->loadFromArray($raw);

		$pos = $this->position;
		$this->position = $swap->position;
		$swap->position = $pos;
		$swap->save();
		$this->save();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Перемещает объект вниз по списку
	 *
	 * @return void
	 *
	 * @since 1.00
	 */
	public function moveDown()
	{
		$q = DB::getHandler()->createSelectQuery();
		$e = $q->expr;
		$q->select('*')->from($this->getDbTable())
			->where($e->lAnd(
				$e->eq($this->ownerProperty,
					$q->bindValue($this->getProperty($this->ownerProperty), null, PDO::PARAM_INT)),
				$e->gt('position', $q->bindValue($this->position, null, PDO::PARAM_INT))
			))
			->orderBy('position', ezcQuerySelect::ASC)
			->limit(1);

		$raw = DB::fetch($q);

		if (!$raw)
		{
			return;
		}

		$class = get_class($this);
		$swap = new $class;
		$swap->loadFromArray($raw);

		$pos = $this->position;
		$this->position = $swap->position;
		$swap->position = $pos;
		$swap->save();
		$this->save();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает экземпляр основного класса плгина
	 *
	 * @param GoodsCatalog $plugin[optional]  Использовать этот экземпляр вместо автоопределения.
	 *                                        Для модульных тестов.
	 * @return GoodsCatalog
	 *
	 * @since 1.00
	 */
	protected static function plugin($plugin = null)
	{
		if ($plugin)
		{
			self::$plugin = $plugin;
		}

		if (!self::$plugin)
		{
			self::$plugin = $GLOBALS['Eresus']->plugins->load('goodscatalog');
		}
		return self::$plugin;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает значение свойства
	 *
	 * Метод не инициирует вызов сеттеров, но обрабатывает значение фильтрами
	 *
	 * @param string $key    Имя свойства
	 * @param mixed  $value  Значение
	 * @return void
	 *
	 * @throws EresusPropertyNotExistsException
	 * @since 1.00
	 */
	protected function setProperty($key, $value)
	{
		$attrs = $this->getAttrs();
		if (!isset($attrs[$key]))
		{
			throw new EresusPropertyNotExistsException($key, get_class($this));
		}

		/*
		 * Фильтруем значение, присваеваемое свойству, в соответствии с типом этого свойства
		 */
		switch ($attrs[$key]['type'])
		{
			case PDO::PARAM_BOOL:
				$value = (boolean) $value;
			break;

			case PDO::PARAM_INT:
				$value = intval($value);
			break;

			case PDO::PARAM_STR:
				$value = $this->filterString($value, $attrs[$key]);
			break;

			default:
				throw new EresusTypeException();
			break;
		}

		$this->propertyCache[$key] = $this->rawData[$key] = $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает значение свойства
	 *
	 * Метод не инициирует вызов геттеров
	 *
	 * @param string $key  Имя свойства
	 * @return mixed
	 *
	 * @throws EresusPropertyNotExistsException
	 * @since 1.00
	 */
	protected function getProperty($key)
	{
		$attrs = $this->getAttrs();
		if (!isset($attrs[$key]))
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
	 * Загружает свойства из массива
	 *
	 * @param array $raw
	 * @return void
	 *
	 * @since 1.00
	 */
	protected function loadFromArray($raw)
	{
		$this->rawData = $raw;
		$this->isNew = false;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Загружает запись из БД по её идентификатору
	 *
	 * @param int $id
	 * @return void
	 *
	 * @throws DBQueryException
	 * @since 1.00
	 */
	protected function loadById($id)
	{
		$db = DB::getHandler();
		$q = $db->createSelectQuery();
		$q->select('*')
			->from($this->getDbTable())
			->where($q->expr->eq('id', $q->bindValue($id,null, PDO::PARAM_INT)))
			->limit(1);

		$this->rawData = DB::fetch($q);

		if (!$this->rawData)
		{
			throw new DomainException("Brand(#$id) not found");
		}

		$this->isNew = false;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяет формат изображения файла
	 *
	 * @param string $mime
	 * @return void
	 */
	protected function checkFormat($mime)
	{
		if (!in_array($mime, $this->supportedFormats))
		{
			throw new EresusRuntimeException("Unsupported file type: $mime",
				iconv('utf-8', 'cp1251', "Неподдерживаемый тип файла: $mime."));
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяет, был ли загружен файл
	 *
	 * @return bool
	 */
	protected function fileUploaded()
	{
		if (!isset($_FILES[$this->upload]) || $_FILES[$this->upload]['error'] == UPLOAD_ERR_NO_FILE)
		{
			return false;
		}

		switch ($_FILES[$this->upload]['error'])
		{
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				throw new RuntimeException(iconv('utf-8', 'cp1251',
					'Размер загружаемого файла превышает максимально допустимый'));
			break;
			case UPLOAD_ERR_PARTIAL:
				throw new RuntimeException(iconv('utf-8', 'cp1251',
					'Во время загрузки файла произошёл сбой. Попробуйте ещё раз'));
				break;
		}

		return true;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Автоматически определяет порядковый номер
	 *
	 * @return void
	 */
	protected function autoPosition()
	{
		$q = DB::getHandler()->createSelectQuery();
		$e = $q->expr;
		$q->select($q->alias($e->max('position'), 'maxval'))
			->from($this->getDbTable())
			->where($e->eq($this->ownerProperty,
				$q->bindValue($this->getProperty($this->ownerProperty), null, PDO::PARAM_INT)));
		$result = DB::fetch($q);
		$this->position = $result['maxval'] + 1;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Потомки могут перекрывать этот метод для загрузки изображения
	 *
	 * @return void
	 *
	 * @since 1.07
	 */
	protected function serveUpload()
	{
	}
	//-----------------------------------------------------------------------------

	/**
	 * Фильтрует значения типа 'string'
	 *
	 * @param mixed $value
	 * @param array $attrs
	 *
	 * @return string
	 *
	 * @since 1.00
	 */
	private function filterString($value, $attrs)
	{
		if (isset($attrs['maxlength']))
		{
			$value = substr($value, 0, $attrs['maxlength']);
		}
		return $value;
	}
	//-----------------------------------------------------------------------------

}
