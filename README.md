[![version](https://img.shields.io/badge/version-1.0.1-green.svg)](https://github.com/steevanb/symfony-validator-constraints/tree/1.0.1)
[![symfony](https://img.shields.io/badge/symfony/validator-^2.3 || ^3.0-blue.svg)](https://symfony.com/)
![Lines](https://img.shields.io/badge/code lines-314-green.svg)
![Total Downloads](https://poser.pugx.org/steevanb/symfony-validator-constraints/downloads)
[![SensionLabsInsight](https://img.shields.io/badge/SensionLabsInsight-platinum-brightgreen.svg)](https://insight.sensiolabs.com/projects/f021d0ec-7046-4ad3-86ca-f1d85f5156f4/analyses/3)
[![Scrutinizer](https://scrutinizer-ci.com/g/steevanb/symfony-validator-constraints/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/steevanb/symfony-validator-constraints/)

symfony-validator-constraints
-----------------------------

Add constraints for [symfony/validators](https://github.com/symfony/validator).

[Changelog](changelog.md)

UniqueObject
------------

UniqueEntity assert new entity is not already in database.

But if you want to assert an object is unique in an array or \Traversable, without accessing database, you can use UniqueObject.

```yml
# Resources/config/validation/Bar.yml
Foo\Bar:
    properties:
        baz:
            - steevanb\SymfonyValidatorConstraints\Constraints\UniqueObject:
                # uniq id to stop error validation when error found,
                # otherwise you will have an error by object in collection
                uniqid: bar_baz
                # properties and getters to generate an "objet identifier",
                # who has to be unique
                properties: [foo, bar]
                getters: [getFoo(), getBar()]
                groups: [add]
                message: translation.message
```
Example :

```yml
Foo\Bar:
    property:
        baz:
            - steevanb\SymfonyValidatorConstraints\Constraints\UniqueObject:
                uniqid: bar_baz
                properties: [foo]
                groups: [add]
```
```php
$object1 = new Foo\Baz();
$object1->foo = 1;
$object1->bar = 2;

$object2 = new Foo\Baz();
$object2->foo = 1;
$object2->bar = 2;

$collection = new Foo\Bar();
$collection->add($object1);
$collection->add($object2);

// errors will contain 1 error, because $object1->foo and $object2->foo are identicals
$errors = $validator->validate($collection, null, ['add']);

// errors will not contain any error
$object1->foo = 2;
$errors = $validator->validate($collection, null, ['add']);
```
