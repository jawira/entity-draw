<?php declare(strict_types=1);

namespace Jawira\EntityDraw\Uml;

use Doctrine\ORM\Mapping\ClassMetadata;

class Inheritance implements ComponentInterface
{
  public function __construct(private readonly ClassMetadata $entity, private readonly string $subClass)
  {
  }

  public function __toString(): string
  {
    $name = $this->entity->getName();
    $subClass = $this->subClass;

    return "$name <|-- $subClass" . PHP_EOL;
  }

}
