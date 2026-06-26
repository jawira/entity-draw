<?php declare(strict_types=1);

namespace Jawira\EntityDraw\Diagram;

use Doctrine\ORM\EntityManagerInterface;

interface DiagramInterface
{
  public function __construct(EntityManagerInterface $entityManager);

  /**
   * @param string[] $include
   * @param string[] $exclude
   */
  public function generateDiagram(string $theme, array $include, array $exclude): string;
}
