<?php

use Doctrine\ORM\Mapping as ORM;

// tag::person[]
class Person
{
  #[ORM\Column]
  public string $name;
  public int $age; // ignored, not a Column
}
// end::person[]
