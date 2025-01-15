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
  private const ABSTRACT = '<<abstract>>';
  private array $properties = [];
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
    $isAbstract = $this->metadata->getReflectionClass()?->isAbstract();
    $abstract = $isAbstract ? self::ABSTRACT : '';
    $this->header = new Raw("class $name $abstract {");
    $this->footer = new Raw('}');
  }

  public function generateProperties(): void
  {
    foreach ($this->metadata->getReflectionProperties() as $property) {
      $this->properties[] = new Property($property);
    }
  }

  public function __toString(): string
  {
    $components = [$this->header, ...$this->properties, $this->footer];

    return $this->toolbox->reduceComponents($components);
  }
}
