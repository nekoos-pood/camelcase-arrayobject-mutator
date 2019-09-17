<?php

namespace Tests\NekoOs\Pood\Support\CamelCaseArrayObjectMutator;

use NekoOs\Pood\Support\CamelCaseArrayObjectMutator;
use Tests\TestCase;

class CamelCaseArrayObjectMutatorTest extends TestCase
{

    /**
     * @var CamelCaseArrayObjectMutator
     */
    private $object;

    /**
     * @testdox Given an instance of CamelCaseArrayObjectMutator when you add value with an empty key then increase index added to instance
     */
    public function testAddValuesAsArrayWithEmptyKey()
    {
        $this->object[] = 1;
        $this->object[] = 2;
        $this->object[] = 3;

        $this->assertCount(3, $this->object);
    }

    /**
     * @testdox Given an instance of CamelCaseArrayObjectMutator when you add value with a key using indeterminate case style then does it
     */
    public function testAddValuesAsArrayWithCaseStyleIndeterminateCaseKey()
    {
        $this->object['one-item'] = 1;
        $this->object['two_item'] = 2;
        $this->object['ThreeItem'] = 3;
        $this->object['Four_item'] = 3;

        $this->assertCount(4, $this->object);

        return $this->object;
    }

    /**
     * @depends testAddValuesAsArrayWithCaseStyleIndeterminateCaseKey
     *
     * @testdox Given an instance of CamelCaseArrayObjectMutator when get object vars then return array with mutate keys as camel case style
     *
     * @param \NekoOs\Pood\Support\CamelCaseArrayObjectMutator $object
     */
    public function testDefaultGetObjectVars(CamelCaseArrayObjectMutator $object)
    {
        $this->assertArrayHasKeys(get_object_vars($object), [
            'oneItem',
            'twoItem',
            'threeItem',
            'fourItem',
        ]);
    }

    /**
     * @depends testAddValuesAsArrayWithCaseStyleIndeterminateCaseKey
     *
     * @testdox Given an instance of CamelCaseArrayObjectMutator when get object vars then return array with original keys style
     *
     * @param \NekoOs\Pood\Support\CamelCaseArrayObjectMutator $object
     */
    public function testAlternativeGetObjectVars(CamelCaseArrayObjectMutator $object)
    {
        $object->behavior(CamelCaseArrayObjectMutator::PREFER_ORIGINAL_KEYS);

        $this->assertArrayHasKeys(get_object_vars($object), [
            'one-item',
            'two_item',
            'ThreeItem',
            'Four_item',
        ]);
    }

    /**
     * @depends                  testAddValuesAsArrayWithCaseStyleIndeterminateCaseKey
     *
     * @testdox                  Given an instance of CamelCaseArrayObjectMutator and debug is enabled when get undefined key then throw ErrorException
     *
     * @param \NekoOs\Pood\Support\CamelCaseArrayObjectMutator $object
     *
     * @expectedException \ErrorException
     * @expectedExceptionMessage Undefined property: NekoOs\Pood\Support\CamelCaseArrayObjectMutator::$undefined
     */
    public function testDebugOnUndefinedKey(CamelCaseArrayObjectMutator $object)
    {
        $object->undefined;
    }

    /**
     * @depends testAddValuesAsArrayWithCaseStyleIndeterminateCaseKey
     *
     * @testdox Given an instance of CamelCaseArrayObjectMutator and debug is disabled when get undefined key return null
     *
     * @param \NekoOs\Pood\Support\CamelCaseArrayObjectMutator $object
     */
    public function testDebugOnUndefinedKeyWithDebugDisabled(CamelCaseArrayObjectMutator $object)
    {
        $object->behavior(~CamelCaseArrayObjectMutator::DEBUG_ON_UNDEFINED);
        $this->assertNull($object->undefined);
    }


    protected function setUp()
    {
        parent::setUp();

        $this->object = new CamelCaseArrayObjectMutator();
    }
}
