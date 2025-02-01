<?php declare(strict_types=1);

$rules = [
  'declare_strict_types' => true,
  'heredoc_to_nowdoc' => true,
  'no_binary_string' => true,
  'no_trailing_whitespace' => true,
  'no_unused_imports' => true,
  'simple_to_complex_string_variable' => true,
  'simplified_if_return' => true,
  'single_blank_line_at_eof' => true,
  'single_quote' => true,
  'switch_case_space' => true,
];

return (new \PhpCsFixer\Config())->setRules($rules)->setHideProgress(true)->setIndent('  ');
