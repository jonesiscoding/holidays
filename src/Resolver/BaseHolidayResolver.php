<?php
/**
 * BaseHolidayResolver.php
 *
 * (c) AMJones <am@jonesiscoding.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DevCoding\Holiday\Resolver;

use DevCoding\Holiday\Date\NormalizerTrait;

/**
 * @author  AMJones <am@jonesiscoding.com>
 * @license https://github.com/jonesiscoding/holidays/blob/main/LICENSE
 * @package DevCoding\Holiday\Resolver
 */
abstract class BaseHolidayResolver implements HolidayResolverInterface
{
  use NormalizerTrait;

  /** @var \DateTimeInterface */
  protected $inception;

  /**
   * @param \DateTimeInterface|null $inception
   *
   * @throws \Exception
   */
  public function __construct($inception = null)
  {
    $this->inception = ($inception instanceof \DateTimeInterface) ? $inception : new \DateTimeImmutable($inception);
  }

  /**
   * @param int $year
   *
   * @return \DateTimeInterface
   */
  abstract protected function getDate(int $year);

  /**
   * @param int    $year
   * @param string $class
   *
   * @return \DateTimeInterface|null
   * @throws \Exception
   */
  final public function resolve($year, $class = \DateTimeImmutable::class)
  {
    if ($date = $this->getDate($this->normalizeYear($year)))
    {
      if ($date > $this->getInception())
      {
        return $date;
      }
    }

    return null;
  }

  /**
   * @param int $hour Integer, 0 - 24
   * @param int $min  Integer, 0 - 60
   * @param int $sec  Integer, 0 - 60
   * @param int $mon  Integer, 1 - 12
   * @param int $day  Integer, 1 - 31
   * @param int $year a four digit integer, signifying a year after 1969
   *
   * @return \DateTime
   *
   * @throws \Exception
   */
  protected function createDateTime(int $hour, int $min, int $sec, int $mon, int $day, int $year)
  {
    $timeString = sprintf('%04d-%02d-%02d %02d:%02d:%02d', $year, $mon, $day, $hour, $min, $sec);

    return new \DateTime( $timeString );
  }

  /**
   * @return \DateTimeInterface
   */
  protected function getInception()
  {
    return $this->inception;
  }

  protected function getPossessive($name)
  {
    return $name.'\''.('s' != $name[strlen($name) - 1] ? 's' : '');
  }
}
