<?php declare(strict_types=1);

namespace Jawira\EntityDraw\Diagram;

use Doctrine\ORM\EntityManagerInterface;
use Jawira\EntityDraw\Services\PlantUmlWriter;
use Jawira\EntityDraw\Services\Toolbox;

class Maxi implements DiagramInterface
{
  private readonly PlantUmlWriter $plantUmlWriter;
  private readonly Toolbox $toolbox;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->toolbox = new Toolbox();
    $this->plantUmlWriter = new PlantUmlWriter($entityManager);
  }

  #[\Override]
  public function generateDiagram(string $theme, array $include, array $exclude): string
  {
    $header = $this->plantUmlWriter->generateHeader($theme);
    $entities = $this->plantUmlWriter->generateEntities($include, $exclude);
    foreach ($entities as $entity) {
      $entity->generateProperties();
      $entity->generateMethods();
    }
    $inheritance = $this->plantUmlWriter->generateInheritance($include, $exclude);
    $relations = $this->plantUmlWriter->generateRelations($include, $exclude);
    $footer = $this->plantUmlWriter->generateFooter();

    return $this->toolbox->reduceComponents([...$header, ...$entities, ...$inheritance, ...$relations, ...$footer]);
  }
}
