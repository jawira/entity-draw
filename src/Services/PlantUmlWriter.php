<?php declare(strict_types=1);

namespace Jawira\EntityDraw\Services;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Jawira\EntityDraw\Uml\Entity;
use Jawira\EntityDraw\Uml\Raw;
use Jawira\EntityDraw\Uml\Inheritance;
use Jawira\EntityDraw\Uml\Relation;

class PlantUmlWriter
{
  private readonly Toolbox $toolbox;

  public function __construct(private readonly EntityManagerInterface $entityManager)
  {
    $this->toolbox = new Toolbox();
  }

  /**
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
   * @return \Jawira\EntityDraw\Uml\Raw[]
   */
  public function generateFooter(): array
  {
    return [new Raw('@enduml')];
  }

  /**
   * @param string[] $exclude
   * @return \Jawira\EntityDraw\Uml\Entity[]
   */
  public function generateEntities(array $exclude): array
  {
    $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
    $filter = function (ClassMetadata $metadata) use ($exclude): bool {
      return !\in_array($metadata->getName(), $exclude, true);
    };
    $metadata = \array_filter($metadata, $filter);
    $entities = \array_map(fn(ClassMetadata $metadata): Entity => new Entity($metadata), $metadata);

    return $entities;
  }

  /**
   * @param string[] $exclude
   * @return Inheritance[]
   */
  public function generateInheritance(array $exclude): array
  {
    $entities = $this->entityManager->getMetadataFactory()->getAllMetadata();

    $inheritance = [];
    foreach ($entities as $entity) {
      if (\in_array($entity->getName(), $exclude, true)) {
        continue;
      }
      foreach ($entity->subClasses as $subClass) {
        if (\in_array($subClass, $exclude, true)) {
          continue;
        }
        $inheritance[] = new Inheritance($entity, $subClass);
      }
    }

    return $inheritance;
  }

  /**
   * @param string[] $exclude
   * @return \Jawira\EntityDraw\Uml\Relation[]
   */
  public function generateRelations(array $exclude): array
  {
    $relations = [];
    $entities = $this->entityManager->getMetadataFactory()->getAllMetadata();
    foreach ($entities as $entity) {
      if (\in_array($entity->getName(), $exclude, true)) {
        continue;
      }
      foreach ($entity->getAssociationMappings() as $associationMapping) {
        if (\in_array($associationMapping['targetEntity'], $exclude, true)) {
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
