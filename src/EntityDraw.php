<?php declare(strict_types=1);

namespace Jawira\EntityDraw;

use Doctrine\ORM\EntityManagerInterface;
use Jawira\DoctrineDiagramContracts\DiagramGeneratorInterface;
use Jawira\DoctrineDiagramContracts\Size;
use Jawira\DoctrineDiagramContracts\Theme;
use Jawira\EntityDraw\Diagram\Midi;
use Jawira\EntityDraw\Diagram\Mini;
use function is_string;

class EntityDraw implements DiagramGeneratorInterface
{
  public function __construct(private readonly EntityManagerInterface $entityManager)
  {
  }

  public function generatePuml(string|Size $size, string|Theme $theme, array $exclude): string
  {
    if (is_string($size)) {
      $size = Size::from($size);
    }

    $diagram = match ($size) {
      Size::Mini => new Mini($this->entityManager),
      Size::Midi, Size::Maxi => new Midi($this->entityManager),
    };

    if ($theme instanceof Theme) {
      $theme = $theme->value;
    }

    return $diagram->generateDiagram($theme, $exclude);
  }
}
