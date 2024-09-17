<?php declare(strict_types=1);

namespace Jawira\EntityDraw\Uml;

/**
 * Raw element is printed as is.
 *
 * @author  Jawira Portugal
 */
class Raw implements ComponentInterface
{
  public function __construct(private string $raw)
  {
  }

  public function __toString(): string
  {
    return $this->raw . PHP_EOL;
  }
}
