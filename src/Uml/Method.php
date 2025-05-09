<?php declare(strict_types=1);

namespace Jawira\EntityDraw\Uml;

use Jawira\EntityDraw\Services\Toolbox;

/**
 * @see https://text.baldanders.info/remark/2018/12/plantuml-3-class-diagrams/
 */
class Method implements ComponentInterface
{
  public function __construct(private readonly \ReflectionMethod $method)
  {
  }

  private function generateVisibility(): string
  {
    return match (true) {
      $this->method->isPublic() => Toolbox::PUBLIC,
      $this->method->isProtected() => Toolbox::PROTECTED,
      $this->method->isPrivate() => Toolbox::PRIVATE,
    };
  }

  private function generateSpecifiers(): string
  {
    $specifiers = '';
    if ($this->method->isStatic()) {
      $specifiers .= '{static}';
    }
    if ($this->method->isAbstract()) {
      $specifiers .= '{abstract}';
    }

    return $specifiers;
  }

  public function generateReturnType(): string
  {

    $returnType = '';
    if (!$this->method->hasReturnType()) {
      return $returnType;
    }
    $returnType .= ': ';
    $returnType .= strval($this->method->getReturnType());

    return $returnType;
  }

  /**
   * @return \Jawira\EntityDraw\Uml\Parameter[]
   */
  public function generateParameters(): array
  {
    $parameters = [];
    foreach ($this->method->getParameters() as $parameter) {
      $parameters[] = new Parameter($parameter);
    }

    return $parameters;
  }

  public function __toString()
  {
    $specifiers = $this->generateSpecifiers();
    $visibility = $this->generateVisibility();
    $name = $this->method->getName();
    $returnType = $this->generateReturnType();
    $parameters = $this->generateParameters();
    $parameters = implode(', ', $parameters);
    return "$specifiers$visibility$name($parameters)$returnType" . PHP_EOL;
  }
}
