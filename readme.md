# FightTheIce Code Generator

```php
<?php

include ('vendor/autoload.php');

$cg = new FightTheIce\Code\ClassGenerator;

$cg->addUse('Laminas\Code\Generator\ClassGenerator','Laminas_ClassGenerator')
->addUse('FightTheIce\Code\TypeHintGenerator')
->addUse('Laminas\Code\Generator\TypeGenerator')
->addUse('Dflydev\DotAccessData\Data')
->addUse('Nette\PhpGenerator\Factory');

$cg->setName('FightTheIce\Code\ClassGenerator');

/*
namespace FightTheIce\Code;

use Dflydev\DotAccessData\Data;
use Laminas\Code\Generator\ClassGenerator as Laminas_ClassGenerator;
use Laminas\Code\Generator\TypeGenerator;
use Nette\PhpGenerator\Factory;

class ClassGenerator
{
}
*/
```

```php
<?php

include ('vendor/autoload.php');

$cg = new FightTheIce\Code\ClassGenerator;

$cg->addUse('Laminas\Code\Generator\ClassGenerator','Laminas_ClassGenerator')
->addUse('FightTheIce\Code\TypeHintGenerator')
->addUse('Laminas\Code\Generator\TypeGenerator')
->addUse('Dflydev\DotAccessData\Data')
->addUse('Nette\PhpGenerator\Factory');

$cg->setName('FightTheIce\Code\ClassGenerator');

/*
namespace FightTheIce\Code;

use Dflydev\DotAccessData\Data;
use Laminas\Code\Generator\ClassGenerator as Laminas_ClassGenerator;
use Laminas\Code\Generator\TypeGenerator;
use Nette\PhpGenerator\Factory;

class ClassGenerator
{
}
*/
```