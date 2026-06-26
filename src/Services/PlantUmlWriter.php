<?php declare(strict_types=1);

namespace Jawira\EntityDraw\Services;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Jawira\EntityDraw\EntityDrawException;
use Jawira\EntityDraw\Uml\Entity;
use Jawira\EntityDraw\Uml\Raw;
use Jawira\EntityDraw\Uml\Inheritance;
use Jawira\EntityDraw\Uml\Relation;
use function array_filter;
use function array_map;
use function is_string;

/**
 * Contains all the logic to write a diagram in PlantUML format.
 */
class PlantUmlWriter
{
  private readonly Toolbox $toolbox;
  private ClassFilter $classFilter;

  public function __construct(private readonly EntityManagerInterface $entityManager)
  {
    $this->toolbox = new Toolbox();
    $this->classFilter = new ClassFilter();
  }

  /**
   * Generate the header of PlantUML diagram.
   *
   * @see https://forum.plantuml.net/1139/please-support-php-namespace-separator
   * @return \Jawira\EntityDraw\Uml\Raw[]
   */
  public function generateHeader(string $theme): array
  {
    return [
      new Raw('@startuml'),
      new Raw('set separator \\ '),
      new Raw('!pragma useIntermediatePackages false'),
      new Raw('skinparam linetype ortho'),
      new Raw('skinparam MinClassWidth 140'),
      new Raw('hide circle'),
      new Raw('hide empty members'),
      new Raw("!theme $theme"),
    ];
  }

  /**
   * Generate the footer of a diagram.
   *
   * @return \Jawira\EntityDraw\Uml\Raw[]
   */
  public function generateFooter(): array
  {
    return [new Raw('@enduml')];
  }

  /**
   * Extract all classes to be used in the diagram.
   *
   * @param string[] $include
   * @param string[] $exclude
   * @return \Jawira\EntityDraw\Uml\Entity[]
   */
  public function generateEntities(array $include, array $exclude): array
  {
    $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
    $filter = (fn(ClassMetadata $metadata): bool => !$this->classFilter->skipEntity($metadata, $include, $exclude));
    $metadata = array_filter($metadata, $filter);
    $entities = array_map(fn(ClassMetadata $metadata): Entity => new Entity($metadata), $metadata);

    return $entities;
  }

  /**
   * Generate inheritance relationships.
   *
   * @param string[] $include
   * @param string[] $exclude
   * @return Inheritance[]
   */
  public function generateInheritance(array $include, array $exclude): array
  {
    $entities = $this->entityManager->getMetadataFactory()->getAllMetadata();

    $inheritance = [];
    foreach ($entities as $entity) {
      if ($this->classFilter->skipEntity($entity, $include, $exclude)) {
        continue;
      }
      foreach ($entity->subClasses as $subClass) {
        if ($this->classFilter->skipClassName($subClass, $include, $exclude)) {
          continue;
        }
        $inheritance[] = new Inheritance($entity, $subClass);
      }
    }

    return $inheritance;
  }

  /**
   * Generate relationships among classes.
   *
   * @param string[] $include
   * @param string[] $exclude
   * @return \Jawira\EntityDraw\Uml\Relation[]
   */
  public function generateRelations(array $include, array $exclude): array
  {
    $relations = [];
    $entities = $this->entityManager->getMetadataFactory()->getAllMetadata();
    foreach ($entities as $entity) {
      if ($this->classFilter->skipEntity($entity, $include, $exclude)) {
        continue;
      }
      foreach ($entity->getAssociationMappings() as $associationMapping) {
        is_string($associationMapping['targetEntity']) or throw new EntityDrawException('targetEntity must be a string value');
        if ($this->classFilter->skipClassName($associationMapping['targetEntity'], $include, $exclude)) {
          continue;
        }
        if ($this->toolbox->isInverseSide($associationMapping)) {
          continue;
        }
        $relations[] = new Relation($entity, $associationMapping);
      }
    }

    return $relations;
  }
}
