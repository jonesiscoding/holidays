<?php

namespace DevCoding\Holiday\Date;

trait NormalizerTrait
{
  /**
   * Evaluates whether the given data is an integer. This includes int, float or string values that are whole numbers.
   *
   * @param mixed $data
   *
   * @return bool
   */
  protected function isInteger($data)
  {
    return is_int($data) || is_numeric($data) && (intval($data) == floatval($data));
  }

  /**
   * @param string|int|float $year
   *
   * @return int
   * @throws \Exception If the given year cannot be normalized or is less than 1970
   */
  protected function normalizeYear($year)
  {
    if ($this->isInteger($year))
    {
      $year = intval($year);

      if ($year < 1970)
      {
        throw new \Exception( sprintf("Years used with %s must be greater than 1970.", get_class()) );
      }

      return $year;
    }
    else
    {
      throw new \Exception(sprintf(
          'Years used with %s must be an integer or numeric string representing a 4 digit calendar year.',
          get_class()
      ));
    }
  }

  /**
   * @param \DateTimeInterface|\DatePeriod|string $criteria
   * @throws \Exception
   */
  protected function normalizeDate($criteria)
  {
    if (!$criteria instanceof \DateTimeInterface && !$criteria instanceof \DatePeriod)
    {
      return new \DateTimeImmutable($criteria);
    }

    return $criteria;
  }
}
