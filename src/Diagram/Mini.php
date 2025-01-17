<?php declare(strict_types=1);

namespace Jawira\EntityDraw\Diagram;

use Doctrine\ORM\EntityManagerInterface;
use Jawira\EntityDraw\Services\PlantUmlWriter;
use Jawira\EntityDraw\Services\Toolbox;

class Mini implements DiagramInterface
{
  private PlantUmlWriter $plantUmlWriter;
  private Toolbox $toolbox;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->toolbox = new Toolbox();
    $this->plantUmlWriter = new PlantUmlWriter($entityManager);
  }

  public function getPlantUmlCode(string $theme,  array $exclusions): string
  {
    $header = $this->plantUmlWriter->generateHeader($theme);
    $entities = $this->plantUmlWriter->generateEntities($exclusions);
    $inheritance = $this->plantUmlWriter->generateInheritance($exclusions);
    $relations = $this->plantUmlWriter->generateRelations($exclusions);
    $footer = $this->plantUmlWriter->generateFooter();

    return $this->toolbox->reduceComponents([...$header, ...$entities, ...$inheritance, ...$relations, ...$footer]);
  }
}
