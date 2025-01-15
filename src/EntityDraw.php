<?php declare(strict_types=1);

namespace Jawira\EntityDraw;

use Doctrine\ORM\EntityManagerInterface;
use Jawira\EntityDraw\Diagram\Midi;
use Jawira\EntityDraw\Diagram\Mini;

class EntityDraw
{
  public function __construct(private readonly EntityManagerInterface $entityManager)
  {
  }

  public function generateDiagram(string $size, string $theme, array $exclusions): string
  {
    $diagram = match ($size) {
      Constants\Size::MINI => new Mini($this->entityManager),
      Constants\Size::MIDI => new Midi($this->entityManager),
    };

    return $diagram->getPlantUmlCode($theme, $exclusions);
  }
}
