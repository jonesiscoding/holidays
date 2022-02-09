<?php
/**
 * FixedHolidayResolver.php
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
class FixedHolidayResolver extends SimpleHolidayResolver
{
  /**
   * @param string|int $year
   *
   * @return \DateTime
   * @throws \Exception
   */
  public function getDate(int $year)
  {
    return $this->createDateTime(0, 0, 0, $this->getMonth(), $this->getDay(), $year);
  }
}