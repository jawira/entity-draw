<?php declare(strict_types=1);

namespace Jawira\EntityDraw\Uml;

use ArrayAccess;
use Doctrine\ORM\Mapping\ClassMetadata;
use Jawira\EntityDraw\EntityDrawException;
use Jawira\EntityDraw\Services\Toolbox;

/**
 * Association between entities.
 *
 * Important, this will only work if $entity parameter is the owning side.
 */
class Relation implements ComponentInterface
{
  /**
   * Copy/paste from DoctrineORM. In v2 this constant is located in
   * `ClassMetadataInfo` class, whilst in v3 was moved to `ClassMetadata`.
   */
  private const ONE_TO_ONE = 1;

  /**
   * Copy/paste from DoctrineORM. In v2 this constant is located in
   * `ClassMetadataInfo` class, whilst in v3 was moved to `ClassMetadata`.
   */
  private const MANY_TO_ONE = 2;

  /**
   * Copy/paste from DoctrineORM. In v2 this constant is located in
   * `ClassMetadataInfo` class, whilst in v3 was moved to `ClassMetadata`.
   */
  private const MANY_TO_MANY = 8;

  private Toolbox $toolbox;

  public function __construct(private ClassMetadata $entity, private array|ArrayAccess $associationMapping)
  {
    $this->toolbox = new Toolbox();
  }

  /**
   * This method uses constants to determine the cardinality, this is done to
   * keep compatibility between **DoctrineORM v2** and **v3**.
   * However, this must be refactored as soon v2 as is not at dependency anymore.
   */
  private function getOwningSideCardinality(): string
  {
    switch ($this->associationMapping['type']) {
      case self::ONE_TO_ONE:
        $cardinality = '1';
        break;
      case self::MANY_TO_ONE:
      case self::MANY_TO_MANY:
        $cardinality = '*';
        break;
      default:
        throw new EntityDrawException('Unknown relation type');
    }

    return $cardinality;
  }

  /**
   * This method uses constants to determine the cardinality, this is done to
   * keep compatibility between **DoctrineORM v2** and **v3**.
   * However, this must be refactored as soon v2 as is not at dependency anymore.
   */
  private function getInverseSideCardinality(): string
  {
    switch ($this->associationMapping['type']) {
      case self::ONE_TO_ONE:
      case self::MANY_TO_ONE:
        $cardinality = '1';
        break;
      case self::MANY_TO_MANY:
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
