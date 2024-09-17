<?php declare(strict_types=1);

namespace Jawira\EntityDraw\Uml;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Jawira\EntityDraw\EntityDrawException;
use Jawira\EntityDraw\Services\Toolbox;

/**
 * Association between entities.
 *
 * Important, this will only work if $entity parameter is the owning side.
 */
class Relation implements ComponentInterface
{
  private Toolbox $toolbox;

  public function __construct(private ClassMetadata $entity, private array $associationMapping)
  {
    $this->toolbox = new Toolbox();
  }

  private function getOwningSideCardinality(): string
  {
    switch ($this->associationMapping['type']) {
      case ClassMetadataInfo::ONE_TO_ONE:
        $cardinality = '1';
        break;
      case ClassMetadataInfo::MANY_TO_ONE:
      case ClassMetadataInfo::MANY_TO_MANY:
        $cardinality = '*';
        break;
      default:
        throw new EntityDrawException('Unknown relation type');
    }

    return $cardinality;
  }

  private function getInverseSideCardinality(): string
  {
    switch ($this->associationMapping['type']) {
      case ClassMetadataInfo::ONE_TO_ONE:
      case ClassMetadataInfo::MANY_TO_ONE:
        $cardinality = '1';
        break;
      case ClassMetadataInfo::MANY_TO_MANY:
        $cardinality = '*';
        break;
      default:
        throw new EntityDrawException('Unknown relation type');
    }

    return $cardinality;
  }

  public function __toString(): string
  {
    $entityName = $this->toolbox->escapeSlash($this->entity->getName());
    $targetEntity = $this->toolbox->escapeSlash($this->associationMapping['targetEntity']);
    $unidirectional = empty($this->associationMapping['inversedBy']) ? '>' : '';

    return sprintf(
      '%s "%s" --%s "%s" %s%s',
      $entityName,
      $this->getOwningSideCardinality(),
      $unidirectional,
      $this->getInverseSideCardinality(),
      $targetEntity,
      PHP_EOL
    );
  }
}
