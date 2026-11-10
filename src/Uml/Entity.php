<?php declare(strict_types=1);

namespace Jawira\EntityDraw\Uml;

use Doctrine\ORM\Mapping\ClassMetadata;
use Jawira\EntityDraw\Services\Toolbox;
use ReflectionProperty;

/**
 * This represents a UML class, but since "class" is a reserved word in PHP it
 * was renamed into "Entity".
 *
 * @author Jawira Portugal
 * @copyright © 2024-2026 Jawira Portugal
 * @license MIT
 */
class Entity implements ComponentInterface
{
  /** @var Property[] */
  private array $properties = [];

  /** @var Method[] */
  private array $methods = [];
  private readonly Toolbox $toolbox;
  private Raw $header;
  private Raw $footer;

  public function __construct(private readonly ClassMetadata $metadata)
  {
    $this->toolbox = new Toolbox();
    $this->generateHeaderAndFooter();
  }

  public function __toString(): string
  {
    $components = [$this->header, ...$this->properties, ...$this->methods, $this->footer];

    return $this->toolbox->reduceComponents($components);
  }

  public function generateProperties(): void
  {
    foreach ($this->metadata->getReflectionProperties() as $property) {
      if (!$property instanceof ReflectionProperty) {
        continue;
      }
      if ($property->getDeclaringClass()->getName() !== $this->metadata->getName()) {
        continue;
      }
      $this->properties[] = new Property($property);
    }
  }

  public function generateMethods(): void
  {
    $methods = $this->metadata->getReflectionClass()->getMethods();
    foreach ($methods as $method) {
      if ($method->getDeclaringClass()->getName() !== $this->metadata->getName()) {
        continue;
      }
      $this->methods[] = new Method($method);
    }
  }

  private function generateHeaderAndFooter(): void
  {
    $name = $this->metadata->getName();
    $header = "class {$name} {";
    $isAbstract = $this->metadata->getReflectionClass()->isAbstract();
    if ($isAbstract) {
      $header = "abstract {$header}";
    }
    $this->header = new Raw($header);
    $this->footer = new Raw('}');
  }
}
