<?php declare(strict_types=1);

namespace Jawira\EntityDraw\Diagram;

use Doctrine\ORM\EntityManagerInterface;
use Jawira\EntityDraw\Services\PlantUmlWritter;
use Jawira\EntityDraw\Services\Toolbox;
use function array_reduce;

class Mini implements DiagramInterface
{
  private PlantUmlWritter $plantUmlWritter;
  private Toolbox $toolbox;

  /**
   * @param string[] $exclusions
   */
  public function __construct(EntityManagerInterface $entityManager, private array $exclusions)
  {
    $this->toolbox = new Toolbox();
    $this->plantUmlWritter = new PlantUmlWritter($entityManager);
  }

  public function getPlantUmlCode(): string
  {
    $header = $this->plantUmlWritter->generateHeader();
    $entities = $this->plantUmlWritter->generateEntities($this->exclusions);
    $inheritance = $this->plantUmlWritter->generateInheritance($this->exclusions);
    $relations = $this->plantUmlWritter->generateRelations($this->exclusions);
    $footer = $this->plantUmlWritter->generateFooter();

    return $this->toolbox->reduceComponents([...$header, ...$entities, ...$inheritance, ...$relations, ...$footer]);
  }
}
