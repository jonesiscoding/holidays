<?php


namespace DevCoding\Holiday\Resolver;


class ComplexHolidayResolver extends BaseHolidayResolver
{

  /** @var \Closure|callable */
  protected $callback;

  /**
   * @param callable|\Closure $callback
   */
  public function __construct($callback, $inception = null)
  {
    $this->callback = $callback;

    parent::__construct($inception);
  }

  protected function getDate(int $year)
  {
    return call_user_func($this->callback, $year);
  }
}