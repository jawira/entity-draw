<?php declare(strict_types=1);

namespace Jawira\EntityDraw\Services;

use function str_ends_with;
use function str_starts_with;

/**
 * This method will normalize and escape special PlantUML characters to display
 * diagrams properly.
 */
class VarNormalizer
{
  /**
   * Converts "NULL" to "null".
   */
  public function lowercaseNull(string $var): string
  {
    if ($var === 'NULL') {
      return 'null';
    }

    return $var;
  }


  /**
   * Replaces "array ()" with "[]".
   *
   * Only the first array will be replaced, nested arrays are kept as-is.
   */
  public function shortArraySyntax(string $varExport): string
  {
    $isArray = str_starts_with($varExport, 'array (') && str_ends_with($varExport, ')');
    if (!$isArray) {
      return $varExport;
    }
    $varExport = substr_replace($varExport, '[', 0, 7);
    $varExport = substr_replace($varExport, ']', -1, 1);

    return $varExport;
  }

  /**
   * Removes all new lines from the input string.
   */
  public function removeNewLines(string $varExport): string
  {
    return strtr($varExport, ["\r" => '', "\n" => '']);
  }

  /**
   * Escape opening and closing parenthesis.
   *
   * Parenthesis can create problems in Class diagrams, any parenthesis will
   * convert a property in a method.
   */
  public function escapeParenthesis(string $varExport): string
  {
    return strtr($varExport, ['(' => '<U+0028>', ')' => '<U+0029>']);
  }

}
