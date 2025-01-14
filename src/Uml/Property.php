<?php declare(strict_types=1);

namespace Jawira\EntityDraw\Uml;

use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionUnionType;
use function is_null;
use function strval;

class Property implements ComponentInterface
{
  private const PRIVATE = '-';
  private const PROTECTED = '#';
  private const PUBLIC = '+';

  /**
   * @see https://stackoverflow.com/questions/41870513/how-to-show-attribute-as-readonly-in-uml
   */
  public function __construct(private readonly \ReflectionProperty $property)
  {
  }


  private function generateVisibility(): string
  {
    return match (true) {
      $this->property->isPublic() => self::PUBLIC,
      $this->property->isProtected() => self::PROTECTED,
      $this->property->isPrivate() => self::PRIVATE,
    };
  }

  public function __toString(): string
  {
    $visibility = $this->generateVisibility();
    $name = $this->property->getName();
    $type = $this->generateType();

    return "$visibility $name $type" . PHP_EOL;
  }

  /**
   * @return string
   */
  public function generateType(): string
  {
    $propertyType = $this->property->getType();
    $type = strval($propertyType);

    return empty($type) ? '' : ": $type";
  }
}
