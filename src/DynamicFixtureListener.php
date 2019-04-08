<?php

namespace folsh\DynamicFixture;

use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestListenerDefaultImplementation;

class DynamicFixtureListener implements TestListener
{
    const REGEX_FUNCTION_PROVIDER = '/^([a-zA-Z0-9_-]+)(\([^\(\)]*\))?$/';
    const REGEX_CLASS_AND_FUNCTION_PROVIDER = '/^([\\a-zA-Z0-9_-]+)::([a-zA-Z0-9_-]+)(\([^\(\)]*\))?$/';
    const REGEX_PARAMS_SPLITER = '/({[^}]*}+|\[[^\]]*\]+|"(?:[^",].)+|[0-9.]+)/';

    private $annotationName;

	use TestListenerDefaultImplementation;

    /**
     * @param string $annotationName
     */
    public function __construct($annotationName = 'setUpContext')
    {
        $this->annotationName = $annotationName;
    }

    /**
     * A test started.
     * @param \PHPUnit_Framework_Test $test
     */
    public function startTest(Test $test)
    {
        //That's nasty, but saved my life :)
        if ($test instanceof Test) {
            $this->setUpContext($test);
        }
    }

    /**
     * @param \PHPUnit_Framework_TestCase $testCase
     */
    private function setUpContext($testCase)
    {
        $annotations = $testCase->getAnnotations();
        if (isset($annotations['method'][$this->annotationName])) {
            $this->callMethods($testCase, $annotations['method'][$this->annotationName]);
        }
    }

    /**
     * @param \PHPUnit_Framework_TestCase $testCase
     * @param array $methods
     * @throws \Exception
     */
    private function callMethods($testCase, array $methods)
    {
        foreach ($methods as $method) {

            if (preg_match(self::REGEX_FUNCTION_PROVIDER, $method, $matches) === 1) {
                $methodName = $matches[1];
                $args = empty($matches[2]) ? "" : $matches[2];

                $this->callMethod($testCase, $methodName, $args);
            } elseif (preg_match(self::REGEX_CLASS_AND_FUNCTION_PROVIDER, $method, $matches) === 1) {

                $className = $matches[1];
                $methodName = $matches[2];
                $args = empty($matches[3]) ? "" : $matches[3];

                $this->callMethod(new $className(), $methodName, $args);
            }
        }
    }

    /**
     * @param $object
     * @param string $methodName
     * @param string $args
     */
    private function callMethod($object, $methodName, $args = "")
    {
        if (method_exists($object, $methodName)) {
            $reflectionMethod = new \ReflectionMethod($object, $methodName);

            if ($reflectionMethod->getNumberOfParameters() == 0) {
                $reflectionMethod->invoke($object);
            } else {
                $reflectionMethod->invokeArgs($object, $this->getArgsFromString($args));
            }
        }
    }

    /**
     * @param string $string
     * @return array: list of simple element: ex: string, array, int
     */
    private function getArgsFromString($string)
    {
        if (preg_match_all(self::REGEX_PARAMS_SPLITER, trim($string, "() "), $matches) === false) {
            return array();
        }

        $result = $matches[1];

        array_walk($result, function (&$item) {
            $item = $this->getItemClean($item);

            if (strpos($item, '[') === 0) { //Is it a table ?
                $item = $this->getStringToArray($item);
            }
        });

        return $result;
    }

    /**
     * "['en'=>'value one','fr'=>'valeur un']"
     *
     * became:
     *
     * array(
     *  'en' => 'value one',
     *  'fr'=>'valeur un'
     * )
     *
     * array multiple is no handle
     *
     * @param $string
     * @return array
     */
    public function getStringToArray($string)
    {
        $arrayResult = array();

        $list = explode(',', trim($string, '[]'));
        foreach ($list as $itemArray) {

            if (strpos($itemArray, '=>') !== false){
                list($key, $value) = explode('=>', $itemArray);

                $arrayResult[$this->getItemClean($key)] = $this->getItemClean($value);

            } else {
                $arrayResult[] = $this->getItemClean($itemArray);
            }
        }

        return $arrayResult;
    }

    /**
     * @param string $string
     * @return string
     */
    private function getItemClean($string)
    {
        return trim(trim($string), "\"'");
    }
}

