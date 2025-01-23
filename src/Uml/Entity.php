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
  private array $properties = [];
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
    $name = $this->toolbox->escapeSlash($this->metadata->getName());
    $header = "class $name {";
    $isAbstract = $this->metadata->getReflectionClass()?->isAbstract();
    if ($isAbstract) {
      $header = "abstract $header";
    }
    $this->header = new Raw($header);
    $this->footer = new Raw('}');
  }

  public function generateProperties(): void
  {
    foreach ($this->metadata->getReflectionProperties() as $property) {
      $this->properties[] = new Property($property);
    }
  }

  public function generateMethods()
  {
    $methods = $this->metadata->getReflectionClass()->getMethods();
    foreach ($methods as $method) {
      $this->methods[] = new Method($method);
    }
  }

  public function __toString(): string
  {
    $components = [$this->header, ...$this->properties, ...$this->methods, $this->footer];

    return $this->toolbox->reduceComponents($components);
  }
}
