<?php

namespace DevCoding\Holiday\Provider;

use DevCoding\Classy\Object\DateTimeString;
use DevCoding\Holiday\Config\HolidayConfig;
use DevCoding\Holiday\Config\HolidayConfigInterface;
use DevCoding\Holiday\Config\InceptionNormalizer;
use DevCoding\Holiday\Config\NameNormalizer;
use DevCoding\Holiday\Date\HolidayDateTime;
use DevCoding\Holiday\Date\HolidayDateTimeImmutable;
use DevCoding\Holiday\Date\NormalizerTrait;
use DevCoding\Holiday\Exception\HolidayNotFoundException;
use DevCoding\Holiday\Resolver\ComplexHolidayResolver;
use DevCoding\Holiday\Resolver\HolidayResolverInterface;
use DevCoding\Holiday\Resolver\SimpleHolidayResolver;

class BaseHolidayProvider implements HolidayProviderInterface
{
  use NormalizerTrait;

  /** @var int */
  protected $year;
  /** @var HolidayConfigInterface[] */
  protected $configs;

  /**
   * @return string[]
   */
  public function all()
  {
    return array_keys($this->getConfigs());
  }

  /**
   * Indicates whether this provider can provide the holiday with the given ID.
   *
   * @param string $id
   *
   * @return bool
   */
  public function has($id)
  {
    $configs = $this->getConfigs();

    return isset($configs[$id]);
  }

  /**
   * Evaluates the given DateTimeInterface to determine if the given date is a holiday provided by this object.
   *
   * @param \DateTimeInterface $criteria
   *
   * @return bool
   *
   * @throws \Exception
   */
  public function isHoliday(\DateTimeInterface $criteria)
  {
    $year = $criteria->format('Y');
    $date = $criteria->format('Ymd');
    foreach ($this->getConfigs() as $config)
    {
      if ($holiday = $config->getResolver()->resolve($year))
      {
        if ($date === $holiday->format('Ymd'))
        {
          return true;
        }
      }
    }

    return false;
  }

//  /**
//   * @param $criteria
//   *
//   * @return \HolidayDateTime[]|\HolidayDateTimeImmutable[]|null
//   *
//   * @throws \Exception
//   */
//  public function getHolidays($criteria)
//  {
//    if ($criteria instanceof \DateTimeInterface)
//    {
//      return $this->getHolidayFromDateTime($criteria);
//    }
//    else
//    {
//      $holidays = [];
//      if ($criteria instanceof \DatePeriod)
//      {
//        foreach ($criteria as $date)
//        {
//          if ($tHolidays = $this->getHolidayFromDateTime($date))
//          {
//            $holidays = array_merge($holidays, $tHolidays);
//          }
//        }
//      }
//      else
//      {
//        try
//        {
//          $year = $this->normalizeYear($criteria);
//        }
//        catch (\Exception $e)
//        {
//          return null;
//        }
//
//        foreach ($this->getConfigs() as $key => $config)
//        {
//          try
//          {
//            if ($holiday = $config->resolver->resolve($year))
//            {
//              $holidays[$key] = $holiday;
//            }
//          }
//          catch (\Exception $e)
//          {
//            continue;
//          }
//        }
//      }
//
//      return $holidays;
//    }
//  }

  protected function add($id, $config)
  {
    if (!isset($this->configs[$id]))
    {
      $this->configs[$id] = $config;
    }

    return $this;
  }

  /**
   * Returns the config object for the given holiday ID.
   *
   * @param string $id
   *
   * @return HolidayConfig|object|null
   */
  public function getConfig($id)
  {
    if ($this->has($id))
    {
      // Normalize Config if Needed
      if (!is_object($this->configs[$id]))
      {
        $this->configs[$id] = new HolidayConfig($id);
      }

      return $this->configs[$id];
    }

    throw new HolidayNotFoundException($id);
  }

  /**
   * Returns all the config objects for holidays that this provider provides.
   *
   * @return HolidayConfigInterface[]
   */
  public function getConfigs()
  {
    foreach ($this->all() as $id)
    {
      if (!is_object($this->configs[$id]))
      {
        $this->getConfig($id);
      }
    }

    return $this->configs;
  }

  /**
   * @return int
   */
  public function getYear()
  {
    return $this->year;
  }

  /**
   * @param $id
   * @param $class
   *
   * @return \DateTimeInterface|void
   */
  public function get($id, $class = \DateTimeImmutable::class)
  {
    if ($this->has($id))
    {
      return $this->getConfig($id)->getResolver()->resolve($this->getYear(), $class);
    }
  }

  public function getHolidays($criteria, $class = null)
  {
    $holidays = [];
    if (!isset($criteria))
    {
      $class = $class ?? \DateTimeImmutable::class;
      $ids = $this->all();
      foreach ($ids as $id)
      {
        $holidays[] = $this->getConfig($id)->getResolver()->resolve($this->getYear(), $class);
      }
    }
    elseif (is_array($criteria))
    {
      foreach ($criteria as $criterion)
      {
        if ($tHolidays = $this->getHolidays($criterion))
        {
          $holidays = array_merge($holidays, $tHolidays);
        }
      }
    }
    elseif($criteria instanceof \DateTimeInterface)
    {
      $class = $class ?? get_class($criteria);
      foreach ($this->getConfigs() as $id => $config)
      {
        $resolved = $config->getResolver()->resolve($this->getYear(), $class);
        if ($resolved->format('Ymd') === $criteria->format('Ymd'))
        {
          $holidays[$id] = $resolved;
        }
      }
    }
    elseif ($criteria instanceof \DatePeriod)
    {
      $class = $class ?? get_class($criteria->getStartDate());
      foreach ($criteria as $date)
      {
        if ($tHolidays = $this->getHolidays($date, $class))
        {
          $holidays = array_merge($holidays, $tHolidays);
        }
      }
    }
    elseif (is_string($criteria))
    {
      if ($this->has($criteria))
      {
        $holidays[$criteria] = $this->getConfig($criteria)->getResolver()->resolve($this->getYear());
      }
      else
      {
        $DateString = new DateTimeString($criteria);
        $isMonStr = $DateString->isStringMonth();
        $isMonNum = $DateString->isNumericMonth();

        if ($isMonNum || $isMonStr)
        {
          if ($isMonNum)
          {
            $start = new \DateTimeImmutable(sprintf('first day of %s-%s', $this->getYear(), $criteria));
            $end   = new \DateTimeImmutable(sprintf('last day of %s-%s', $this->getYear(), $criteria));
          }
          else
          {
            $start = new \DateTimeImmutable(sprintf('first day of %s %s', $criteria, $this->getYear()));
            $end   = new \DateTimeImmutable(sprintf('last day of %s %s', $criteria, $this->getYear()));
          }

          $new = new \DatePeriod($start, new \DateInterval('P1D'), $end);
        }
        elseif ($md = $DateString->getMonthDay())
        {
          $m = new DateTimeString($md['month']);

          if ($m->isStringMonth())
          {
            $new = new \DateTimeImmutable(sprintf('%s %s %s', $md['month'], $md['day'], $this->getYear()));
          }
          else
          {
            $new = new \DateTimeImmutable(sprintf('%s-%s-%s', $this->getYear(), $md['month'], $md['day']));
          }
        }

        if (isset($new))
        {
          if ($tHolidays = $this->getHolidays($new, $class))
          {
            $holidays = array_merge($holidays, $tHolidays);
          }
        }
      }
    }

    return !empty($holidays) ? $holidays : null;
  }
}
