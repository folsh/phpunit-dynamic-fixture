<?php

namespace folsh\DynamicFixture;

use PHPUnit_Framework_Test;

class DynamicFixtureListener extends \PHPUnit_Framework_BaseTestListener
{
    const REGEX_FUNCTION_PROVIDER = '/([a-zA-Z0-9._-]+)(\([^\(\)]*\))?/';
    const REGEX_PARAMS_SPLITER = '/({[^}]*}+|\[[^\]]*\]+|[^,]+)/';

    private $annotationName;

    /**
     * @param string $annotationName
     */
    public function __construct($annotationName = 'setUpContext')
    {
        $this->annotationName = $annotationName;
    }

    /**
     * A test started.
     * @param  PHPUnit_Framework_Test $test
     */
    public function startTest(PHPUnit_Framework_Test $test)
    {
        //That's nasty, but saved my life :)
        if ($test instanceof \PHPUnit_Framework_TestCase) {
            $this->setUpContext($test);
        }
    }

    /**
     * @param \PHPUnit_Framework_TestCase $testCase
     */
    private function setUpContext(\PHPUnit_Framework_TestCase $testCase)
    {
        $annotations = $testCase->getAnnotations();
        if (isset($annotations['method'][$this->annotationName])) {
            $this->callMethods($testCase, $annotations['method'][$this->annotationName]);
        }

    }

    /**
     * @param \PHPUnit_Framework_TestCase $testCase
     * @param array $methods
     */
    private function callMethods(\PHPUnit_Framework_TestCase $testCase, array $methods)
    {
        foreach ($methods as $method) {
            if (preg_match(self::REGEX_FUNCTION_PROVIDER, $method, $matches) !== 1){
                continue;
            }

            $methodName = $matches[1];

            if (!method_exists($testCase, $methodName)) {
                continue;
            }

            $reflectionMethod = new \ReflectionMethod($testCase, $methodName);

            if ($reflectionMethod->getNumberOfParameters() == 0) {
                $reflectionMethod->invoke($testCase);
            } else {
                $reflectionMethod->invokeArgs($testCase, $this->getArgsFromString($matches[2]));
            }
        }
    }

    /**
     * @param string $string
     * @return array: list of simple element: ex: string, array, int
     */
    private function getArgsFromString($string)
    {
        if (preg_match_all(self::REGEX_PARAMS_SPLITER, trim($string, "() "), $matches) === false){
            return array();
        }

        $result = $matches[1];

        array_walk($result, function(&$item){
            if (strpos(trim($item), '[') === 0){ //Is it a table ?
                $item = array_map(function($item){return $this->getItemClean(trim($item));}, explode(',', trim($item, '[] ')));
            } else { //Other types
                $item = $this->getItemClean($item);
            }

        });

        return $result;
    }

    /**
     * @param string $string
     * @return string
     */
    private function getItemClean($string){
        return trim($string, "\"'");
    }
}
