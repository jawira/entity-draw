<?php declare(strict_types=1);

namespace Jawira\EntityDraw\Diagram;

/**
 * @internal
 */
interface DiagramInterface
{
  public function getPlantUmlCode(): string;
}
