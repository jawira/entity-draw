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

  public function generateDiagram(string $theme, array $exclude): string
  {
    $header = $this->plantUmlWriter->generateHeader($theme);
    $entities = $this->plantUmlWriter->generateEntities($exclude);
    $inheritance = $this->plantUmlWriter->generateInheritance($exclude);
    $relations = $this->plantUmlWriter->generateRelations($exclude);
    $footer = $this->plantUmlWriter->generateFooter();

    return $this->toolbox->reduceComponents([...$header, ...$entities, ...$inheritance, ...$relations, ...$footer]);
  }
}
