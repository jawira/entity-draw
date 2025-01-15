<?php declare(strict_types=1);

namespace Jawira\EntityDraw\Diagram;

use Doctrine\ORM\EntityManagerInterface;
use Jawira\EntityDraw\Services\PlantUmlWritter;
use Jawira\EntityDraw\Services\Toolbox;

class Midi implements DiagramInterface
{
  private PlantUmlWritter $plantUmlWritter;
  private Toolbox $toolbox;

  /**
   * @param string[] $exclusions
   */
  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->toolbox = new Toolbox();
    $this->plantUmlWritter = new PlantUmlWritter($entityManager);
  }

  public function getPlantUmlCode(string $theme, array $exclusions): string
  {
    $header = $this->plantUmlWritter->generateHeader($theme);
    $entities = $this->plantUmlWritter->generateEntities($exclusions);
    foreach ($entities as $entity) {
      $entity->generateProperties();
    }
    $inheritance = $this->plantUmlWritter->generateInheritance($exclusions);
    $relations = $this->plantUmlWritter->generateRelations($exclusions);
    $footer = $this->plantUmlWritter->generateFooter();

    return $this->toolbox->reduceComponents([...$header, ...$entities, ...$inheritance, ...$relations, ...$footer]);
  }
}
