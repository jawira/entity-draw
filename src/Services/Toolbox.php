<?php declare(strict_types=1);

namespace Jawira\EntityDraw\Services;

use ArrayAccess;
use Jawira\EntityDraw\Uml\ComponentInterface;

class Toolbox
{
  private const SLASH = '\\';
  public const PRIVATE = '-';
  public const PROTECTED = '#';
  public const PUBLIC = '+';
  /**
   * Reduce an array of {@see ComponentInterface} objects to array.
   *
   * @param \Jawira\EntityDraw\Uml\ComponentInterface[] $components
   * @return string
   */
  public function reduceComponents(array $components): string
  {
    $reducer = function (string $carry, ComponentInterface $component): string {
      return $carry . \strval($component);
    };

    return \array_reduce($components, $reducer, '');
  }

  /**
   * Escape back-slash in class namespaces to be displayed in PlantUml diagrams.
   *
   * @param string $fqcn
   * @return string
   */
  public function escapeSlash(string $fqcn): string
  {
    return \addcslashes($fqcn, self::SLASH);
  }

  /**
   * Tells if current entity is the inverse side using mapping data.
   *
   * @param string[] $associationMapping
   * @return bool
   */
  public function isInverseSide(array|ArrayAccess $associationMapping): bool
  {
    return !empty($associationMapping['mappedBy']);
  }
}
