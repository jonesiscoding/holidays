<?php

namespace DevCoding\Holiday\Exception;

use Psr\Container\NotFoundExceptionInterface as PsrNotFoundExceptionInterface;

class HolidayNotFoundException extends \Exception implements PsrNotFoundExceptionInterface
{
}
