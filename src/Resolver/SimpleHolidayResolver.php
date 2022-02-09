<?php
/**
 * SimpleHolidayResolver.php
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
abstract class SimpleHolidayResolver extends BaseHolidayResolver
{
  /** @var string|int */
  protected $month;
  /** @var string|int */
  protected $day;
  /** @var \DateTimeImmutable */
  protected $inception;

  /**
   * @param int|string $month
   * @param int|string $day
   * @param \DateTimeImmutable|string|null $inception
   */
  public function __construct($month, $day, $inception = null)
  {
    $this->month     = $month;
    $this->day       = $day;

    parent::__construct($inception);
  }

  /**
   * @return int|string
   */
  protected function getMonth()
  {
    return $this->month;
  }

  /**
   * @return int|string
   */
  protected function getDay()
  {
    return $this->day;
  }

  /**
   * @return \DateTimeImmutable
   */
  protected function getInception()
  {
    return $this->inception;
  }
}