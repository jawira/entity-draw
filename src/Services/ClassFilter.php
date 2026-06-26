<?php declare(strict_types=1);

namespace Jawira\EntityDraw\Services;

use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Tells if a class should appear in the diagram or not.
 */
class ClassFilter
{
  /**
   * Tells if an entity of class {@see \Doctrine\ORM\Mapping\ClassMetadata} must be removed from the class diagram.
   *
   * @param string[] $include
   * @param string[] $exclude
   */
  public function skipEntity(ClassMetadata $entity, array $include, array $exclude): bool
  {
    return $this->skipClassName($entity->getName(), $include, $exclude);
  }

  /**
   * Tells is provided class-name should be removed from the diagram.
   *
   * @param string[] $include
   * @param string[] $exclude
   */
  public function skipClassName(string $className, array $include, array $exclude): bool
  {
    // This variable exists to avoid automatic refactoring by tools like PHPStan
    // and Rector, allowing to see "Include" and "Exclude" blocks clearly.
    $defaultValue = false;

    // Include
    $includeHasClassName = boolval(count($include));
    $isClassNameInInclude = $this->isClassNameInArray($className, $include);
    if ($includeHasClassName && !$isClassNameInInclude) {
      return true;
    }

    // Exclude
    $excludeHasClassName = boolval(count($exclude));
    $isClassNameInExclude = $this->isClassNameInArray($className, $exclude);
    if ($excludeHasClassName && $isClassNameInExclude) {
      return true;
    }

    return $defaultValue;
  }

  /**
   * Checks if a class name is in the provided array.
   *
   * @param string[] $classNameArray
   */
  private function isClassNameInArray(string $className, array $classNameArray): bool
  {
    foreach ($classNameArray as $table) {
      if (fnmatch($table, $className, FNM_NOESCAPE)) {
        return true;
      }
    }

    return false;
  }
}
