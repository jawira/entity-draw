<?php declare(strict_types=1);

namespace Jawira\EntityDraw\Uml;

use Doctrine\ORM\Mapping\ClassMetadata;
use Jawira\EntityDraw\Services\Toolbox;

class Relation implements ComponentInterface
{
  private Toolbox $toolbox;

  public function __construct(private ClassMetadata $entity, private array $associationMapping)
  {
    $this->toolbox = new Toolbox();
  }

  public function __toString(): string
  {
    $entityName = $this->toolbox->escapeSlash($this->entity->getName());
    $targetEntity = $this->toolbox->escapeSlash($this->associationMapping['targetEntity']);
    $unidirectional = empty($this->associationMapping['inversedBy']) ? '>' : '';

    return "$entityName --$unidirectional $targetEntity" . PHP_EOL;
  }
}
