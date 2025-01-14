<?php declare(strict_types=1);

namespace Jawira\EntityDraw;

use Doctrine\ORM\EntityManagerInterface;
use Jawira\EntityDraw\Diagram\Midi;
use Jawira\EntityDraw\Diagram\Mini;

class EntityDraw
{
  public function __construct(private EntityManagerInterface $entityManager)
  {
  }

  public function generateDiagram(string $size, string $theme, array $exclusions): string
  {
    $diagram =  new Midi($this->entityManager, $exclusions);

    return $diagram->getPlantUmlCode();
  }
}
