<?php


namespace AndyDorff\SherpaXML\Handler;


final class HandlerId
{
    /**
     * @var mixed
     */
    private $value;

    public function __construct($value = null)
    {
        $this->value = $value ?? spl_object_hash($this);
    }

    public static function fromString(string $handlerId): self
    {
        return new self($handlerId);
    }

    public function __toString(): string
    {
        return strval($this->value);
    }
}