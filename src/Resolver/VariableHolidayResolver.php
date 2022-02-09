<?php
/**
 * HolidayResolverInterface.php
 *
 * (c) AMJones <am@jonesiscoding.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DevCoding\Holiday\Resolver;

/**
 * @author  AMJones <am@jonesiscoding.com>
 * @license https://github.com/jonesiscoding/holidays/blob/main/LICENSE
 * @package DevCoding\Holiday\Resolver
 */
class VariableHolidayResolver extends SimpleHolidayResolver
{
  /**
   * @param int $year
   *
   * @return \DateTime
   * @throws \Exception
   */
  public function getDate(int $year)
  {
    $month = $this->getNormalizedMonth();
    $day   = $this->getNormalizedDay();

    if (isset($month))
    {
      if (is_numeric($day))
      {
        $phrase = sprintf('%s-%s-%s', $year, $month, $day);
      }
      else
      {
        $phrase = sprintf('%s of %s-%s',$day, $year, $month);
      }
    }
    else
    {
      $phrase = sprintf('%s of %s', $day, $year);
    }

    return new \DateTime($phrase);
  }

  private function getNormalizedDay()
  {
    $day = $this->getDay();
    if (!is_numeric($day))
    {
      if (substr($day, - 2) === 'of')
      {
        return trim(substr($day, 0, -2));
      }
    }

    return $day;
  }

  private function getNormalizedMonth()
  {
    $month = $this->getMonth();
    if (isset($month) && !is_numeric($month))
    {
      if ($date = date_parse($month))
      {
        return $date['month'] ?? $month;
      }
    }

    return $month;
  }
}