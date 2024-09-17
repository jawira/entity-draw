<?php declare(strict_types=1);

namespace Jawira\EntityDraw\Uml;

use Doctrine\ORM\Mapping\ClassMetadata;
use Jawira\EntityDraw\Services\Toolbox;

class Inheritance implements ComponentInterface
{
  private Toolbox $toolbox;

  public function __construct(private ClassMetadata $entity, private string $subClass)
  {
    $this->toolbox = new Toolbox();
  }

  public function __toString(): string
  {
    $name = $this->toolbox->escapeSlash($this->entity->getName());
    $subClass = $this->toolbox->escapeSlash($this->subClass);

    return "$name <|-- $subClass" . PHP_EOL;
  }

}
