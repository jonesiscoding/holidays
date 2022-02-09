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
 * Interface for classes that resolve the a \DateTimeInterface for a holiday.
 *
 * @author  AMJones <am@jonesiscoding.com>
 * @license https://github.com/jonesiscoding/holidays/blob/main/LICENSE
 * @package DevCoding\Holiday\Resolver
 */
interface HolidayResolverInterface
{
  const TYPES = [
      self::COMPLEX  => ComplexHolidayResolver::class,
      self::VARIABLE => VariableHolidayResolver::class,
      self::FIXED    => FixedHolidayResolver::class,
  ];

  const VARIABLE = 'variable';
  const FIXED    = 'fixed';
  const COMPLEX  = 'complex';

  /**
   * Must resolve the date of the holiday within the given year, returning the class given.
   *
   * @param string|int $year
   * @param string     $class
   *
   * @return \DateTimeImmutable
   */
  public function resolve($year, $class = \DateTimeImmutable::class);
}

