<?php
/**
 * HolidayConfigInterface.php
 *
 * (c) AMJones <am@jonesiscoding.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DevCoding\Holiday\Config;

use DevCoding\Holiday\Resolver\ComplexHolidayResolver;
use DevCoding\Holiday\Resolver\FixedHolidayResolver;
use DevCoding\Holiday\Resolver\HolidayResolverInterface;
use DevCoding\Holiday\Resolver\VariableHolidayResolver;

/**
 * Object representing a string that consists of PHP date format characters.
 *
 * @author  AMJones <am@jonesiscoding.com>
 * @license https://github.com/jonesiscoding/holidays/blob/main/LICENSE
 * @package DevCoding\Holiday\Config
 */
interface HolidayConfigInterface
{
  const RESOLVER_VARIABLE = 'variable';
  const RESOLVER_FIXED    = 'fixed';
  const RESOLVER_COMPLEX  = 'complex';

  const RESOLVERS = [
      self::RESOLVER_COMPLEX  => ComplexHolidayResolver::class,
      self::RESOLVER_VARIABLE => VariableHolidayResolver::class,
      self::RESOLVER_FIXED    => FixedHolidayResolver::class,
  ];

  /**
   * @return string
   */
  public function getSlug();

  /**
   * @return string
   */
  public function getName();

  /**
   * @return HolidayResolverInterface
   */
  public function getResolver();
}