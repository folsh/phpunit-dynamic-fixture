<?php

class ExampleWithListenerAndClassTest extends PHPUnit_Framework_TestCase {

    /**
     *
     */
    public function testNoSetUp()
    {
        $this->assertNull(User::$name);
        $this->assertNull(User::$firstName);
    }

    /**
     * @setUpContext User::setUpName()
     */
    public function testSetUpName()
    {
        $this->assertSame('Name', User::$name);
        $this->assertNull(User::$firstName);
    }

    /**
     *
     * @setUpContext User::setUpNameAndFirstName("Durant","Cedric")
     */
    public function testSetUpNameAndFirstName()
    {
        $this->assertSame('Durant', User::$name);
        $this->assertSame('Cedric', User::$firstName);
    }
}

class User
{
    public static $name;
    public static $firstName;

    public function setUpName()
    {
        self::$name = 'Name';
    }

    public function setUpFirstName()
    {
        self::$firstName = 'FirstName';
    }

    public function setUpNameAndFirstName($name, $firstName)
    {
        self::$name = $name;
        self::$firstName = $firstName;
    }
}
