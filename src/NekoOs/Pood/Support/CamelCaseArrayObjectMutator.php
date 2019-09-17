<?php

namespace NekoOs\Pood\Support;

use ArrayIterator;
use ArrayObject;
use ErrorException;

/**
 * @package NekoOs\Pood\Support
 * @author  Neder Alfonso Fandiño Andrade (neafand@gmail.com)
 */
class CamelCaseArrayObjectMutator extends ArrayObject
{

    const PREFER_ORIGINAL_KEYS = 1;
    const DEBUG_ON_UNDEFINED = 2;

    /**
     * @var int
     */
    private static $defaultFlags = null;

    /**
     * @var bool
     */
    private $debug = true;

    /**
     * @var int
     */
    private $flags = 0;

    /**
     * @var array
     */
    private $storage = [];

    /**
     * @var bool
     */
    private $mutate = false;

    /**
     * @var array
     */
    private $keys = [];

    /**
     * @var array
     */
    private $values = [];

    /**
     * @param array  $input
     * @param int    $flags
     * @param string $iterator_class
     */
    public function __construct($input = [], $flags = self::ARRAY_AS_PROPS, $iterator_class = ArrayIterator::class)
    {
        if ($input instanceof self) {
            $input = $input->getStorage();
        }

        parent::__construct($input, $flags, $iterator_class);

        $this->behavior(static::$defaultFlags ?? ~static::PREFER_ORIGINAL_KEYS | static::DEBUG_ON_UNDEFINED, true);
    }

    /**
     * @param int  $flags
     * @param bool $restart
     */
    public static function defaultBehavior(int $flags, bool $restart = false)
    {
        if ($restart) {
            static::$defaultFlags = $flags;
        } else {
            BitwiseFlag::set(static::$defaultFlags, $flags, true);
        }
    }

    /**
     * @param int  $flags
     *
     * @param bool $restart
     *
     * @return $this
     */
    public function behavior(int $flags)
    {
        $this->setBehaviorFlags($flags);

        $this->debug = BitwiseFlag::match($this->flags, static::DEBUG_ON_UNDEFINED);
        $this->mutate = !BitwiseFlag::match($this->flags, static::PREFER_ORIGINAL_KEYS);

        return $this->rearrangeStorage();
    }

    /**
     * @param mixed $input
     *
     * @return array
     */
    public function exchangeArray($input)
    {
        $this->storage = [];
        $response = parent::exchangeArray([]);

        if ($input instanceof self) {
            $input = $input->getStorage();
        }

        foreach ($input as $key => $value) {
            $this->offsetSet($key, $value);
        }

        return $response;
    }

    /**
     * @return array
     */
    public function getStorage(): array
    {
        return $this->storage;
    }

    /**
     * @param int|string $index
     *
     * @return bool
     */
    public function offsetExists($index)
    {
        return !empty($this->getKeyAliases($index));
    }

    /**
     * @param int|string $index
     *
     * @return mixed
     * @throws \ErrorException
     */
    public function offsetGet($index)
    {
        $response = $this->values[camel_case($index)] ?? new ErrorException('Undefined property: ' . static::class . '::$' . $index);

        $isException = $response instanceof ErrorException;

        if ($isException && $this->debug) {
            throw $response;
        } elseif ($isException) {
            $response = null;
        }

        return $response;
    }

    /**
     * @param mixed $index
     * @param mixed $value
     */
    public function offsetSet($index, $value)
    {
        $alias = is_null($index) ? $this->counter() : $index;

        $this->addAlias($alias, $value);

        foreach ($this->getKeyAliases($alias) as $keyAlias) {
            $this->offset($keyAlias, $value);
        }

    }

    public function counter()
    {
        $array = array_filter(array_keys((array)$this), 'is_integer');
        return empty($array) ? 0 : max($array) + 1;
    }

    /**
     * @param int|string $index
     */
    public function offsetUnset($index)
    {
        foreach ($this->getKeyAliases($index) as $alias) {
            unset($this->storage[$alias]);
            parent::offsetUnset($alias);
        }
        $this->removeAliases($index);
    }

    /**
     * @param int $flags
     */
    private function setBehaviorFlags(int $flags)
    {
        $this->debug = false;
        $this->mutate = true;
        $response = 0;

        $behaviorFlags = [
            static::DEBUG_ON_UNDEFINED,
            static::PREFER_ORIGINAL_KEYS,
        ];

        foreach ($behaviorFlags as $behaviorFlag) {
            if (BitwiseFlag::match($flags, $behaviorFlag)) {
                $response |= $behaviorFlag;
                $this->debug = true;
            } elseif (BitwiseFlag::match($flags, ~$behaviorFlag)) {
                $response |= ~$behaviorFlag;
            } elseif (BitwiseFlag::match($this->flags, $behaviorFlag)) {
                $response |= $behaviorFlag;
                $this->debug = true;
            } elseif (BitwiseFlag::match($this->flags, ~$behaviorFlag)) {
                $response |= ~$behaviorFlag;
            }
        }

        $this->flags = $response;
    }

    /**
     * @return $this
     */
    private function rearrangeStorage()
    {
        $localStorage = $this->storage;
        $foreignStorage = get_object_vars($this);
        $mutateStorage = $this->values;

        if (empty($mutateStorage)) {
            $input = [];
        } elseif (array_diff_key($localStorage, $mutateStorage)) {
            $input = $localStorage;
        } else {
            $input = $foreignStorage;
        }

        $this->exchangeArray($input);

        return $this;
    }

    /**
     * @param int|string $index
     * @param mixed      $value
     */
    private function addAlias($index, $value): void
    {
        $key = camel_case($index);

        $this->keys[$key][] = $index;
        $this->values[$key] = $value;

        array_unique($this->keys[$key]);
    }

    /**
     * @param int|string $index
     *
     * @return array
     */
    private function getKeyAliases($index): array
    {
        return $this->keys[camel_case($index)] ?? [];
    }

    /**
     * @param int|string $index
     */
    private function removeAliases($index)
    {
        $key = camel_case($index);
        unset(
            $this->keys[$key],
            $this->values[$key]
        );
    }

    /**
     * @param int|string $index
     * @param mixed      $value
     */
    private function offset($index, $value)
    {
        $localKey = $key = camel_case($index);
        $foreignKey = $index;
        if ($this->mutate) {
            $localKey = $index;
            $foreignKey = $key;
        }
        $this->storage[$localKey] = $value;
        parent::offsetSet($foreignKey, $value);
        $this->values[$key] = $value;
    }
}