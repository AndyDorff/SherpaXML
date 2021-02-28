<?php

namespace AndyDorff\SherpaXML;

use AndyDorff\SherpaXML\Interpreters\AbstractInterpreter;
use AndyDorff\SherpaXML\Interpreters\SherpaXMLInterpreter;
use AndyDorff\SherpaXML\Interpreters\SimpleXMLInterpreter;
use AndyDorff\SherpaXML\Misc\ParseResult;
use ReflectionFunction;
use XMLReader;

final class Parser
{
    private ParseResult $parseResult;
    /**
     * @var AbstractInterpreter[]
     */
    private array $interpreters = [];
    private bool $isBreaked = false;

    public function __construct(array $interpreters = [])
    {
        $this->parseResult = new ParseResult();
        $this->registerMultipleInterpreters(array_merge(
            [new SimpleXMLInterpreter()],
            $interpreters
        ));
    }

    public function parseResult(): ParseResult
    {
        return $this->parseResult;
    }

    public function registerMultipleInterpreters(array $interpreters): void
    {
        array_walk($interpreters, [$this, 'registerInterpreter']);
    }

    public function registerInterpreter(AbstractInterpreter $interpreter): void
    {
        $this->interpreters[$interpreter->className()] = $interpreter;
    }

    /**
     * @return AbstractInterpreter[]
     */
    public function interpreters(): array
    {
        return $this->interpreters;
    }

    public function getInterpreter(string $className): ?AbstractInterpreter
    {
        return ($this->interpreters[$className] ?? null);
    }

    public function break(): void
    {
        $this->isBreaked = true;
    }

    public function parse(SherpaXML $xml): ParseResult
    {
        $this->isBreaked = false;
        while(
            $xml->moveToNextElement()
            && !$this->isBreaked
        ){
            $this->doParse($xml);
        }

        return $this->parseResult;
    }

    private function doParse(SherpaXML $xml): void
    {
        $this->parseResult->totalCount++;
        if($handler = $xml->getHandler($xml->getCurrentPath())){
            $params = $this->resolveHandleParams($handler->asClosure(), $xml);
            $handler->__invoke(...$params);
            $this->parseResult->parseCount++;
        }
    }

    private function resolveHandleParams(\Closure $handle, SherpaXML $xml)
    {
        $result = [];
        $handle = new ReflectionFunction($handle);
        foreach($handle->getParameters() as $key => $parameter) {
            $name = $parameter->getType()->getName();
            if ($name === self::class){
                $result[$key] = $this;
            } elseif ($name === ParseResult::class) {
                $result[$key] = $this->parseResult;
            } elseif ($name === SherpaXML::class) {
                $result[$key] = $xml;
            } elseif ($interpreter = $this->getInterpreter($name)){
                $result[$key] = $interpreter->interpret($xml);
            } else {
                $result[$key] = null;
            }
        }

        return $result;
    }
}
