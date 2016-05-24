<?php

class ExampleWithListenerAndParametersTest extends PHPUnit_Framework_TestCase
{
    private $string1;
    private $string2;
    private $list;
    private $jsonToArray;
    private $empty;
    private $params;

    /**
     * @param $string
     */
    public function setUpString1($string)
    {
        $this->string1 = $string;
    }

    /**
     * @param $string1
     * @param $string2
     */
    public function setUpString1AndString2($string1, $string2)
    {
        $this->string1 = $string1;
        $this->string2 = $string2;
    }

    /**
     * @setUpContext setUpString1("Jean Claude")
     */
    public function testSetUpName()
    {
        $this->assertSame("Jean Claude", $this->string1);
        $this->assertNull($this->string2);
    }

    /**
     * @setUpContext setUpString1AndString2("Dupond"," Jules")
     */
    public function testSetUpNameAndFirstame()
    {
        $this->assertSame("Dupond", $this->string1);
        $this->assertSame(" Jules", $this->string2);
    }

    /**
     * @param array $list
     */
    public function setUpArray(array $list)
    {
        $this->list = $list;
    }

    /**
     * @setUpContext setUpArray([ 'Elise', " Thibault"])
     */
    public function testSetUpArray()
    {
        $this->assertInternalType('array', $this->list);
        $this->assertCount(2, $this->list);
        $this->assertSame('Elise', $this->list[0]);
        $this->assertSame(' Thibault', $this->list[1]);
    }

    /**
     * @setUpContext setUpArray(["Elise"=>" Durant", "Thibault " => "Dupont"])
     */
    public function testSetUpAssociativeArray()
    {
        $this->assertInternalType('array', $this->list);
        $this->assertCount(2, $this->list);

        $this->assertArrayHasKey('Elise', $this->list);
        $this->assertArrayHasKey('Thibault ', $this->list);

        $this->assertSame(' Durant', $this->list["Elise"]);
        $this->assertSame('Dupont', $this->list["Thibault "]);
    }

    /**
     * @param string $json
     */
    public function setUpJson($json)
    {
        $this->jsonToArray = json_decode($json, true);
    }

    /**
     * @setUpContext setUpJson({"MyProject\\Entity\\User": {"id":"1","firstname":"toto","lastname":"tata"}})
     */
    public function testSetUpJson()
    {
        $this->assertInternalType('array', $this->jsonToArray);
    }

    /**
     */
    public function setUpEmpty()
    {
        $this->empty = 'No param';
    }

    /**
     * @setUpContext setUpEmpty()
     */
    public function testSetUpEmpty()
    {
        $this->assertSame('No param', $this->empty);
    }

    /**
     * @param $string1
     * @param $list1
     * @param $integer1
     * @param $list2
     * @param $string2
     * @param $json
     */
    public function setUpComplex($string1, array $list1, $integer1, array $list2, $string2, $json)
    {
        $this->params = array(
            'string1' => $string1,
            'list1' => $list1,
            'integer1' => $integer1,
            'list2' => $list2,
            'string2' => $string2,
            'json' => json_decode($json, true)
        );
    }

    /**
     * @setUpContext setUpComplex("aaa",[10,11],99,["bbb","ccc"],"xyz",{"ddd": {"eee":"fff","ggg":"hhh"}})
     */
    public function testSetUpComplex()
    {
        $this->assertInternalType('array', $this->params);

        $this->assertSame('aaa', $this->params['string1']);

        $this->assertCount(2, $this->params['list1']);
        $this->assertSame('10', $this->params['list1'][0]); //Not convert in integer
        $this->assertSame('11', $this->params['list1'][1]); //Not convert in integer

        $this->assertSame('99', $this->params['integer1']); //Not convert in integer

        $this->assertCount(2, $this->params['list2']);
        $this->assertSame('bbb', $this->params['list2'][0]);
        $this->assertSame('ccc', $this->params['list2'][1]);

        $this->assertSame('xyz', $this->params['string2']);

        $this->assertInternalType('array', $this->params['json']);
        $this->assertNotEmpty($this->params['json']['ddd']);
        $this->assertNotEmpty($this->params['json']['ddd']['eee']);
        $this->assertNotEmpty($this->params['json']['ddd']['ggg']);

        $this->assertSame('fff', $this->params['json']['ddd']['eee']);
        $this->assertSame('hhh', $this->params['json']['ddd']['ggg']);
    }
}