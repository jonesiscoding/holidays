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
use DevCoding\Holiday\Resolver\HolidayResolverInterface;
use DevCoding\Holiday\Resolver\SimpleHolidayResolver;

/**
 * Object representing a string that consists of PHP date format characters.
 *
 * @author  AMJones <am@jonesiscoding.com>
 * @license https://github.com/jonesiscoding/holidays/blob/main/LICENSE
 * @package DevCoding\Holiday\Config
 */
class HolidayConfig implements HolidayConfigInterface
{
  /** @var string */
  protected $name;
  /** @var string */
  protected $slug;
  /** @var HolidayResolverInterface */
  protected $resolver;
  /** @var bool */
  protected $possessive;
  /** @var string */
  protected $id;
  /** @var \DateTimeInterface|null */
  private $inception;

  public function __construct($config)
  {
    if (!isset($config['name']))
    {
      throw new \InvalidArgumentException('Bad Holiday Config: You must provide a "name".');
    }

    $this->id         = $config['id'] ?? null;
    $this->inception  = $this->resolveInception($config);
    $this->name       = $config['name'];
    $this->possessive = $config['possessive'] ?? null;
    $this->resolver   = $this->resolverResolver($config);
    $this->slug       = $config['slug'] ?? null;
  }

  /**
   * @return string
   */
  public function getId()
  {
    if (!isset($this->id))
    {
      $find = ["s'", 's’', "'s", '’s', ' ', '_', '-'];
      $repl = ['s', 's', ''];

      $this->id = str_replace($find, $repl, ucwords($this->getName(), ' _-'));
    }

    return $this->id;
  }

  /**
   * @return bool
   */
  public function isPossessive()
  {
    if (!isset($this->possessive))
    {
      $this->possessive = (bool)preg_match('#\b(.*)(\'s|’s|s’|s\')\b#', $this->getName());
    }

    return $this->possessive;
  }

  /**
   * Evaluates if the resolver in the given config is a child of SimpleHolidayResolver or not.
   *
   * @param $config
   *
   * @return bool|null
   */
  protected function isSimpleResolver($config)
  {
    if ($resolver = $config->resolver ?? null)
    {
      if (is_string($resolver))
      {
        if (in_array($resolver, [HolidayConfigInterface::RESOLVER_VARIABLE, HolidayConfigInterface::RESOLVER_FIXED]))
        {
          return true;
        }

        return class_exists($resolver) && is_a($resolver, SimpleHolidayResolver::class);
      }
    }

    return null;
  }

  public function getInception()
  {
    return $this->inception;
  }

  /**
   * @return string
   */
  public function getSlug()
  {
    if (!isset($this->slug))
    {
      return strtolower(trim( preg_replace( '#[^0-9a-z]+#i', '-', $this->getName() ), ' -' ));
    }

    return $this->slug;
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @return HolidayResolverInterface
   */
  public function getResolver()
  {
    return $this->resolver;
  }

  /**
   * @param array $config
   *
   * @return \DateTimeInterface|null
   * @throws \Exception
   */
  protected function resolveInception($config)
  {
    $inception = $config['inception'] ?? null;
    if (isset($inception) && !$inception instanceof \DateTimeInterface)
    {
      $inception = new \DateTimeImmutable($inception);
    }

    return $inception;
  }

  /**
   * Normalizes and returns the resolver from the given config.  Returned resolver is a HolidayResolverInterface object.
   *
   * @param HolidayConfig|object $config
   *
   * @return HolidayResolverInterface
   */
  protected function resolverResolver($config)
  {
    // Change String Resolver to Class Name Resolver
    $config = (object)$config;
    $config->resolver = $config->resolver ?? HolidayResolverInterface::FIXED;
    $config->resolver = HolidayResolverInterface::TYPES[$config->resolver] ?? $config->resolver;

    if ($this->isSimpleResolver($config))
    {
      return new $config->resolver($config->month, $config->day, $config->inception);
    }
    else
    {
      $resolver = $config->resolver ?? null;
      if (is_object($resolver))
      {
        return $resolver;
      }
      elseif (is_callable($resolver))
      {
        return new ComplexHolidayResolver($resolver, $config->inception ?? null);
      }

      throw new \InvalidArgumentException('Bad Config');
    }
  }
}