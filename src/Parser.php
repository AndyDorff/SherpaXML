<?php

namespace AndyDorff\SherpaXML;

use AndyDorff\SherpaXML\Handler\AbstractClosureHandler;
use AndyDorff\SherpaXML\Handler\Handler;
use AndyDorff\SherpaXML\Interpreters\AbstractInterpreter;
use AndyDorff\SherpaXML\Interpreters\SimpleXMLInterpreter;
use AndyDorff\SherpaXML\Misc\HandlerManager;
use AndyDorff\SherpaXML\Misc\ParseResult;
use ReflectionFunction;

final class Parser
{
    private ParseResult $parseResult;
    /**
     * @var AbstractInterpreter[]
     */
    private array $interpreters = [];
    private bool $isBreaked = false;
    /**
     * @var HandlerManager[]
     */
    private array $handlers = [];

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
            do{
                $oneMore = $this->doParse($xml);
            } while ($oneMore);
        }
        //Edge case: On parse end -- invoke all remaining handlers
        $this->completeRemainingHandlers();

        return $this->parseResult;
    }

    private function doParse(SherpaXML $xml): bool
    {
        $this->parseResult->totalCount++;
        $elementPath = $xml->getCurrentPath();
        if($handler = $xml->getHandler($elementPath)){
            $this->handlers[] = $handlerManager = $this->getHandlerManager($handler, $xml);
            if(!$handlerManager->isDeferred()){
                $this->invokeHandlers();
            }

            $this->parseResult->parseCount++;
        }

        return ($handler !== $xml->getHandler($elementPath));
    }

    private function getHandlerManager(AbstractClosureHandler $handler, SherpaXML $xml): HandlerManager
    {
        $handlerManager = new HandlerManager($handler, $xml->getCurrentPath());
        $this->setUpHandlerManager($handlerManager, $xml);

        return $handlerManager;
    }

    /**
     * @param HandlerManager $handlerManager
     * @param SherpaXML $xml
     * @throws \ReflectionException
     */
    private function setUpHandlerManager(HandlerManager $handlerManager, SherpaXML $xml): void
    {
        $params = [];
        $handle = new ReflectionFunction($handlerManager->handler()->asClosure());
        foreach($handle->getParameters() as $key => $parameter) {
            $name = $parameter->getType()->getName();
            if ($name === self::class){
                $params[$key] = $this;
            } elseif ($name === Handler::class) {
                $params[$key] = $handlerManager->handler();
            } elseif ($name === ParseResult::class) {
                $params[$key] = $this->parseResult;
            } elseif ($name === SherpaXML::class) {
                $params[$key] = $xml;
            } elseif ($interpreter = $this->getInterpreter($name)){
                $params[$key] = $interpreter->interpret($xml);
                if(method_exists($interpreter, 'isReady')){
                    $handlerManager->waitForInterpreter($interpreter, $params[$key]);
                }
            } else {
                $params[$key] = null;
            }
        }
        $handlerManager->setParams($params);
    }

    private function invokeHandlers(): void
    {
        $prevHandler = null;
        foreach($this->handlers as $index => $handlerManager){
            $handlerManager->invoke();
            if($prevHandler && !$handlerManager->isNestedFor($prevHandler)){
                $prevHandler->complete();
                unset($this->handlers[$index - 1]);
            }
            $prevHandler = $handlerManager;
        }
    }

    private function completeRemainingHandlers(): void
    {
        foreach($this->handlers as $handlerManager){
            $handlerManager->invoke(true);
            $handlerManager->complete();
        }

        $this->handlers = [];
    }
}
