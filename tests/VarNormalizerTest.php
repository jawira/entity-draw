<?php

use Jawira\EntityDraw\Services\VarNormalizer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(VarNormalizer::class)]
class VarNormalizerTest extends TestCase
{
  private VarNormalizer $varNormalizer;

  protected function setUp(): void
  {
    $this->varNormalizer = new VarNormalizer();
  }

  #[DataProvider('escapeParenthesisProvider')]
  public function testEscapeParenthesis($value, $expected): void
  {
    $actual = $this->varNormalizer->escapeParenthesis($value);
    $this->assertEquals($expected, $actual);
  }

  public static function escapeParenthesisProvider(): array
  {
    return [
      ['()', '<U+0028><U+0029>'],
      ['(dev)', '<U+0028>dev<U+0029>'],
      ['(((', '<U+0028><U+0028><U+0028>'],
      ["array (\n)", "array <U+0028>\n<U+0029>"],
    ];
  }

  #[DataProvider('removeNewLineProvider')]
  public function testRemoveNewLine($value, $expected): void
  {
    $actual = $this->varNormalizer->removeNewLines($value);
    $this->assertEquals($expected, $actual);
  }

  public static function removeNewLineProvider(): array
  {
    return [
      ["\n", ''],
      ["\n\n\n\n", ''],
      ["\r\n\r\n", ''],
      ["Foo\r\nBar\r\nBaz\r\n", 'FooBarBaz'],
    ];
  }

  #[DataProvider('shortArraySyntaxProvider')]
  public function testShortArraySyntax($value, $expected): void
  {

    $actual = $this->varNormalizer->shortArraySyntax($value);

    $this->assertEquals($expected, $actual);
  }

  public static function shortArraySyntaxProvider(): array
  {
    return [
      ["array ()", "[]"],
      ["array (\n\n\n)", "[\n\n\n]"],
      ["array (xxxxxxxxxxx)", "[xxxxxxxxxxx]"],
      ["array (array ())", "[array ()]"],
    ];
  }

  #[DataProvider('lowercaseNullProvider')]
  public function testLowercaseNull($value, $expected): void
  {
    $actual = $this->varNormalizer->lowercaseNull($value);

    $this->assertEquals($expected, $actual);
  }

  public static function lowercaseNullProvider(): array
  {
    return [
      ['NULL', 'null'],
      ['NULL ', 'NULL '],
      ['NuLl', 'NuLl'],
      ['null', 'null'],
      ['nulL', 'nulL'],
      ['Kano', 'Kano'],
    ];
  }
}
