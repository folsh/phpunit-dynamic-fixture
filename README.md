[![SensioLabsInsight](https://insight.sensiolabs.com/projects/b3876f8b-864e-4bdc-8de6-0902fe5be1b4/mini.png)](https://insight.sensiolabs.com/projects/b3876f8b-864e-4bdc-8de6-0902fe5be1b4)
[![Build Status](https://api.travis-ci.org/Nikoms/phpunit-dynamic-fixture.png)](https://api.travis-ci.org/Nikoms/phpunit-dynamic-fixture)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/Nikoms/phpunit-dynamic-fixture/badges/quality-score.png)](https://scrutinizer-ci.com/g/Nikoms/phpunit-dynamic-fixture/)
[![Code Coverage](https://scrutinizer-ci.com/g/Nikoms/phpunit-dynamic-fixture/badges/coverage.png)](https://scrutinizer-ci.com/g/Nikoms/phpunit-dynamic-fixture/)


DynamicFixture
==============

Thanks to annotations, this library allows you to call dynamic/custom "setUp" methods before each one of your test.
It eases the understanding of your test because you explicitly set which context/variables you will use.
It also can speed up your tests as you only initialize what they need.

Installation
--------------

### Composer ###
Simply add this to your `composer.json` file:
```js
"require": {
    "folsh/phpunit-dynamic-fixture": "dev-master"
}
```

Then run `php composer.phar install`

PhpUnit configuration
---------------------
To activate the plugin. Add the listener to your phpunit.xml(.dist) file:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit>
    ...
    <listeners>
        <listener class="Nikoms\DynamicFixture\DynamicFixtureListener" file="vendor/nikoms/phpunit-dynamic-fixture/src/DynamicFixtureListener.php" />
    </listeners>
</phpunit>
```

Usage
-----

Use the annotation "@setUpContext" to call the specified method(s) just before the test. Of course, you can add as many annotations as you want.

```php
class MyTest extends PHPUnit_Framework_TestCase {

    private $name;

    /**
     * Must be public
     */
    public function setUpName()
    {
        $this->name = 'Nicolas';
    }

    public function initContext()
    {
        //...
    }

    /**
     * @setUpContext setUpName
     * @setUpContext initContext
     * @setUpContext ...
     *
     */
    public function testSetUpName()
    {
        //$this->name is "Nicolas"
    }
}
```

Params
------

If you want to add some parameters in your function, add them between brackets.
Warning: at this time, you can only add simple types (see exemples)
Types accepted:
- string
- json
- array (single level)
- numeric (convert in string !)

```php
class MyTest extends PHPUnit_Framework_TestCase {

    private $name1;
    private $name2;
    private $list;

    /**
     * Must be public
     */
    public function setUpNames($name1, $name2)
    {
        $this->name1 = $name1;
        $this->name2 = $name2;
    }

    public function initContext()
    {
        //...
    }

    /**
     * @setUpContext setUpNames("Nicolas", "Cedric")
     */
    public function testSetUpNames()
    {
        //$this->name1 is "Nicolas"
        //$this->name2 is "Cedric"
    }
}
```

```php
class MyTest extends PHPUnit_Framework_TestCase {

    private $list;

    /**
     * Must be public
     */
    public function setUpArray(array $list)
    {
        $this->list = $list;
    }

    public function initContext()
    {
        //...
    }

    /**
     * @setUpContext setUpArray(["Nicolas", "Cedric"])
     */
    public function testSetUpArray()
    {
        //$this->list is array("Nicolas", "Cedric")
    }
}
```

Customize
---------

If you don't like the name of the annotation, you can change it by passing a new one in the constructor:

```xml
 <listener class="Nikoms\DynamicFixture\DynamicFixtureListener" file="vendor/nikoms/phpunit-dynamic-fixture/src/DynamicFixtureListener.php">
    <arguments>
        <string>myCustomSetUp</string>
    </arguments>
</listener>
```
