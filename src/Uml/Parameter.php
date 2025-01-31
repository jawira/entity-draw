<?php declare(strict_types=1);

namespace Jawira\EntityDraw\Uml;

use ReflectionParameter;
use function strval;

class Parameter implements ComponentInterface
{
  public function __construct(private readonly ReflectionParameter $parameter)
  {
  }

  public function generateType(): string
  {
    $parameterType = $this->parameter->getType();

    $type = strval($parameterType);

    return empty($type) ? '' : " : $type";
  }

  public function generateName(): string
  {
    $name = '';
    if ($this->parameter->isPassedByReference()) {
      $name .= '&';
    }
    if ($this->parameter->isVariadic()) {
      $name .= '...';
    }
    $name .= $this->parameter->getName();

    return $name;
  }

  public function generateDefaultValue(): string
  {
    if (!$this->parameter->isOptional() || !$this->parameter->isDefaultValueAvailable()) {
      return '';
    }
    /** @var mixed $value */
    $value = $this->parameter->getDefaultValue();

    return ' = ' . var_export($value, true);

  }

  public function __toString(): string
  {
    $type = $this->generateType();
    $name = $this->generateName();
    $defaultValue = $this->generateDefaultValue();

    return "$name$type$defaultValue";
  }
}
