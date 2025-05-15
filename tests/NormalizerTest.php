<?php

use Jawira\EntityDraw\Services\VarNormalizer;
use PHPUnit\Framework\TestCase;

class NormalizerTest extends TestCase
{
  private VarNormalizer $varNormalizer;

  protected function setUp(): void
  {
    $this->varNormalizer = new VarNormalizer();
  }

  /**
   * @covers \Jawira\EntityDraw\Services\VarNormalizer::escapeParenthesis
   * @dataProvider escapeParenthesisProvider
   */
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

  /**
   * @covers       \Jawira\EntityDraw\Services\VarNormalizer::removeNewLines
   * @dataProvider removeNewLineProvider
   */
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

  /**
   * @covers       \Jawira\EntityDraw\Services\VarNormalizer::shortArraySyntax
   * @dataProvider shortArraySyntaxProvider
   */
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

  /**
   * @covers       \Jawira\EntityDraw\Services\VarNormalizer::lowercaseNull
   * @dataProvider lowercaseNullProvider
   */
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
