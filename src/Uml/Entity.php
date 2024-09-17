<?php declare(strict_types=1);

namespace Jawira\EntityDraw\Uml;

use Doctrine\ORM\Mapping\ClassMetadata;
use http\Header;
use Jawira\EntityDraw\Services\Toolbox;
use PhpParser\Builder\Class_;

/**
 * This represents a UML class, but since "class" is a reserved word in PHP it
 * was renamed into "Entity".
 */
class Entity implements ComponentInterface
{
  private Toolbox $toolbox;
  private Raw $header;
  private Raw $footer;

  public function __construct(private ClassMetadata $metadata)
  {
    $this->toolbox = new Toolbox();
    $name = $this->toolbox->escapeSlash($this->metadata->getName());
    $this->header = new Raw("class $name {");
    $this->footer = new Raw('}');
  }

  public function __toString()
  {
    $components = [$this->header, $this->footer];

    return $this->toolbox->reduceComponents($components);
  }
}
