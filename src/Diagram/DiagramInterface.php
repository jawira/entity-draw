<?php declare(strict_types=1);

namespace Jawira\EntityDraw\Diagram;

use Doctrine\ORM\EntityManagerInterface;

interface DiagramInterface
{
  public function __construct(EntityManagerInterface $entityManager);

  public function generateDiagram(string $theme, array $exclude): string;
}
