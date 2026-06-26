<?php declare(strict_types=1);

namespace Jawira\EntityDraw;

use Doctrine\ORM\EntityManagerInterface;
use Jawira\DoctrineDiagramContracts\DiagramGeneratorInterface;
use Jawira\DoctrineDiagramContracts\Size;
use Jawira\DoctrineDiagramContracts\Theme;

/**
 * This is the only entrypoint to use this library.
 */
class EntityDraw implements DiagramGeneratorInterface
{
  public function __construct(private readonly EntityManagerInterface $entityManager)
  {
  }

  #[\Override]
  public function generatePuml(Size $size, string|Theme $theme, array $include, array $exclude): string
  {
    $diagram = match ($size) {
      Size::Mini => new Diagram\Mini($this->entityManager),
      Size::Midi => new Diagram\Midi($this->entityManager),
      Size::Maxi => new Diagram\Maxi($this->entityManager),
    };
    if ($theme instanceof Theme) {
      $theme = $theme->value;
    }

    return $diagram->generateDiagram($theme, $include, $exclude);
  }
}
