<?php


namespace Tests;


use PHPUnit\Framework\Assert as PHPUnit;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * @param mixed|array $actual
     * @param array $expected
     */
    public static function assertArrayHasKeys($actual, array $expected)
    {

        PHPUnit::assertIsArray($actual);

        foreach ($expected as $value) {
            PHPUnit::assertArrayHasKey($value, $actual);
        }
    }
}