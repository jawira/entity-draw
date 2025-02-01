<?php declare(strict_types=1);

namespace Jawira\EntityDraw\Uml;

use Jawira\EntityDraw\Services\Toolbox;
use function strval;
use function var_export;

class Property implements ComponentInterface
{
  /**
   * @see https://stackoverflow.com/questions/41870513/how-to-show-attribute-as-readonly-in-uml
   */
  public function __construct(private readonly \ReflectionProperty $property)
  {
  }


  private function generateVisibility(): string
  {
    return match (true) {
      $this->property->isPublic() => Toolbox::PUBLIC,
      $this->property->isProtected() => Toolbox::PROTECTED,
      $this->property->isPrivate() => Toolbox::PRIVATE,
    };
  }

  private function generateDefaultValue(): string
  {
    if (!$this->property->hasDefaultValue()) {
      return '';
    }
    /** @var mixed $value */
    $value = $this->property->getDefaultValue();

    return ' = ' . var_export($value, true);
  }

  public function generateType(): string
  {
    /** @var \ReflectionNamedType|\ReflectionUnionType|\ReflectionIntersectionType|null $propertyType */
    $propertyType = $this->property->getType();
    $type = strval($propertyType);

    return empty($type) ? '' : " : $type";
  }

  /**
   * @see https://stackoverflow.com/questions/41870513/how-to-show-attribute-as-readonly-in-uml
   */
  private function generateReadOnly(): string
  {
    $isReadOnly = $this->property->isReadOnly();
    return $isReadOnly ? ' {readonly}' : '';
  }

  public function __toString(): string
  {
    $visibility = $this->generateVisibility();
    $name = $this->property->getName();
    $type = $this->generateType();
    $defaultValue = $this->generateDefaultValue();
    $readOnly = $this->generateReadOnly();

    return "$visibility$name$type$defaultValue$readOnly" . PHP_EOL;
  }
}
