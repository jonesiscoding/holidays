<?php
/**
 * HolidayProviderInterface.php
 *
 * (c) AMJones <am@jonesiscoding.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DevCoding\Holiday\Provider;

use DevCoding\Holiday\Config\HolidayConfig;
use DevCoding\Holiday\Config\HolidayConfigInterface;
use DevCoding\Holiday\Exception\HolidayNotFoundException;

/**
 * Interface for classes to represent objects that provide a batch of holiday configurations and resolved holidays.
 *
 * @author  AMJones <am@jonesiscoding.com>
 * @license https://github.com/jonesiscoding/holidays/blob/main/LICENSE
 * @package DevCoding\Holiday\Provider
 */
interface HolidayProviderInterface
{
  /**
   * @return HolidayConfigInterface[]
   */
  public function getConfigs();

  /**
   * @param string $id
   *
   * @return HolidayConfig
   */
  public function getConfig($id);

  /**
   * @return int
   */
  public function getYear();

  /**
   * @return string[]
   */
  public function all();

  /**
   * @param string $id
   *
   * @return bool
   */
  public function has($id);

  /**
   * @param string $id
   *
   * @return \DateTimeInterface
   * @throws HolidayNotFoundException
   */
  public function get($id, $class = \DateTimeImmutable::class);

  /**
   * @param \DatePeriod|\DateTimeInterface|string $criteria
   *
   * @return \DateTimeInterface[]|null
   */
  public function getHolidays($criteria, $class = \DateTimeImmutable::class);

  /**
   * @param \DateTimeInterface $criteria
   *
   * @return bool
   */
  public function isHoliday(\DateTimeInterface $criteria);
}