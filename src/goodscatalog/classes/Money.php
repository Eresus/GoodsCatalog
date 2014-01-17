<?php
/**
 * Деньги
 *
 * @version ${product.version}
 *
 * @copyright 2010, ООО "Два слона", http://dvaslona.ru/
 * @license http://www.gnu.org/licenses/gpl.txt GPL License 3
 * @author Михаил Красильников <mk@dvaslona.ru>
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
 */


/**
 * Деньги
 *
 * @package GoodsCatalog
 *
 * @since 1.02
 */
class GoodsCatalog_Money
{
    /**
     * Количество денег
     *
     * @var float
     */
    private $amount;

    /**
     * Создаёт новый объект "Деньги"
     *
     * @param float $amount начальное количество денег
     *
     * @since 1.02
     */
    public function __construct($amount = 0.0)
    {
        $this->setAmount($amount);
    }

    /**
     * Задаёт количество денег
     *
     * @param GoodsCatalog_Money|float|string $amount
     *
     * @return void
     *
     * @since 1.02
     */
    public function setAmount($amount)
    {
        switch (true)
        {
            case is_numeric($amount):
                $this->amount = $amount;
                break;
            case is_string($amount):
                $lc = localeconv();
                // Удаляем разделители тысяч
                $amount = str_replace($lc['mon_thousands_sep'], '', $amount);
                // Меняем разделитель дробной части на точку
                $amount = str_replace(array($lc['mon_decimal_point'], $lc['decimal_point']), '.', $amount);
                // Удаляем лишние символы
                $amount = preg_replace('/[^\d\.]/', '', $amount);
                $this->amount = floatval($amount);
                break;
            case $amount instanceof self:
                $this->amount = $amount->amount;
                break;
        }
    }

    /**
     * Возвращает количество денег
     *
     * @return float
     *
     * @since 1.02
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Возвращает строковое представление денежной суммы
     *
     * @return string
     *
     * @since 1.02
     */
    public function __toString()
    {
        $lc = localeconv();
        $hasDecimals = $this->amount != round($this->amount);
        if ($lc['mon_decimal_point'] == '')
        {
            $lc['mon_decimal_point'] = '.';
        }
        return number_format($this->amount, $hasDecimals ? 2 : 0, $lc['mon_decimal_point'],
            $lc['mon_thousands_sep']);
    }
}

