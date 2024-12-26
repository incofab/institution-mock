<?php
namespace App\Support;

use ArrayObject;

class Res extends ArrayObject
{
    function __construct(protected array $data = [])
    {
        parent::__construct($data, ArrayObject::ARRAY_AS_PROPS);
    }

    public static function make(bool $success, $message = '', array $data = [])
    {
        return new static([
            'success' => $success,
            'message' => $message,
            ...$data,
        ]);
    }

    function __get($name): mixed
    {
        return $this->offsetGet($name);
    }

    function __set($name, $value): void
    {
        $this->offsetSet($name, $value);
    }

    function offsetExists(mixed $key): bool
    {
        return isset($this->data[$key]);
    }

    function offsetSet(mixed $key, mixed $value): void
    {
        if (is_null($key)) {
            $this->data[] = $value;
        } else {
            $this->data[$key] = $value;
        }
    }

    function offsetGet(mixed $key): mixed
    {
        // if (!isset($this->data[$key])) {
        //     return null;
        // }

        // $this->data[$key] = is_callable($this->data[$key])
        //     ? $this->data[$key]($this)
        //     : $this->data[$key];

        return $this->data[$key] ?? null;
    }

    public function offsetUnset(mixed $key): void
    {
        unset($this->data[$key]);
    }
    public function toArray()
    {
        return $this->data;
    }
    public function isSuccessful(): bool
    {
        return $this->data['success'] ?? false;
    }
    public function isNotSuccessful(): bool
    {
        return $this->isSuccessful() === false;
    }
    public function getMessage(): string
    {
        return $this->data['message'] ?? '';
    }
}
