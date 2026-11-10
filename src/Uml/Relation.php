<?php declare(strict_types=1);

namespace Jawira\EntityDraw\Uml;

use ArrayAccess;
use Doctrine\ORM\Mapping\ClassMetadata;
use Jawira\EntityDraw\EntityDrawException;

use function is_string;
use function sprintf;

/**
 * Association between entities.
 *
 * Important, this will only work if $entity parameter is the owning side.
 *
 * @author Jawira Portugal
 * @copyright © 2024-2026 Jawira Portugal
 * @license MIT
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

  public function __construct(private readonly ClassMetadata $entity, private array|ArrayAccess $associationMapping) {}

  public function __toString(): string
  {
    $entityName = $this->entity->getName();
    $targetEntity = $this->associationMapping['targetEntity'];
    if (!is_string($targetEntity)) {
      throw new EntityDrawException('Cannot determine target entity name.');
    }
    $targetEntity = $targetEntity;
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

  /**
   * This method uses constants to determine the cardinality, this is done to
   * keep compatibility between **DoctrineORM v2** and **v3**.
   * However, this must be refactored as soon v2 as is not at dependency anymore.
   */
  private function getOwningSideCardinality(): string
  {
    $cardinality = match ($this->associationMapping['type']) {
      self::ONE_TO_ONE => '1',
      self::MANY_TO_ONE, self::MANY_TO_MANY => '*',
      default => throw new EntityDrawException('Unknown relation type'),
    };

    return $cardinality;
  }

  /**
   * This method uses constants to determine the cardinality, this is done to
   * keep compatibility between **DoctrineORM v2** and **v3**.
   * However, this must be refactored as soon v2 as is not at dependency anymore.
   */
  private function getInverseSideCardinality(): string
  {
    $cardinality = match ($this->associationMapping['type']) {
      self::ONE_TO_ONE, self::MANY_TO_ONE => '1',
      self::MANY_TO_MANY => '*',
      default => throw new EntityDrawException('Unknown relation type'),
    };

    return $cardinality;
  }
}
