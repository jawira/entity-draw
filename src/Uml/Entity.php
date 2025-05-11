<?php declare(strict_types=1);

namespace Jawira\EntityDraw\Uml;

use Doctrine\ORM\Mapping\ClassMetadata;
use Jawira\EntityDraw\Services\Toolbox;

/**
 * This represents a UML class, but since "class" is a reserved word in PHP it
 * was renamed into "Entity".
 */
class Entity implements ComponentInterface
{
  /** @var \Jawira\EntityDraw\Uml\Property[] */
  private array $properties = [];
  /** @var \Jawira\EntityDraw\Uml\Method[] */
  private array $methods = [];
  private Toolbox $toolbox;
  private Raw $header;
  private Raw $footer;

  public function __construct(private ClassMetadata $metadata)
  {
    $this->toolbox = new Toolbox();
    $this->generateHeaderAndFooter();
  }


  private function generateHeaderAndFooter(): void
  {
    $name = $this->metadata->getName();
    $header = "class $name {";
    $isAbstract = $this->metadata->getReflectionClass()->isAbstract();
    if ($isAbstract) {
      $header = "abstract $header";
    }
    $this->header = new Raw($header);
    $this->footer = new Raw('}');
  }

  public function generateProperties(): void
  {
    foreach ($this->metadata->getReflectionProperties() as $property) {
      if (!$property instanceof \ReflectionProperty) {
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

  public function __toString(): string
  {
    $components = [$this->header, ...$this->properties, ...$this->methods, $this->footer];

    return $this->toolbox->reduceComponents($components);
  }
}
