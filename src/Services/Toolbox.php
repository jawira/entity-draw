<?php declare(strict_types=1);

namespace Jawira\EntityDraw\Services;

use ArrayAccess;
use Jawira\EntityDraw\Uml\ComponentInterface;
use function array_reduce;
use function strval;

class Toolbox
{
  public const PRIVATE = '-';
  public const PROTECTED = '#';
  public const PUBLIC = '+';
  /**
   * Reduce an array of {@see ComponentInterface} objects to array.
   *
   * @param \Jawira\EntityDraw\Uml\ComponentInterface[] $components
   */
  public function reduceComponents(array $components): string
  {
    $reducer = function (string $carry, ComponentInterface $component): string {
      return $carry . strval($component);
    };

    return array_reduce($components, $reducer, '');
  }

  /**
   * Tells if current entity is the inverse side using mapping data.
   */
  public function isInverseSide(array|ArrayAccess $associationMapping): bool
  {
    return isset($associationMapping['mappedBy']) && $associationMapping['mappedBy'] !== '';
  }
}
