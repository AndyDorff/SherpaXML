<?php

namespace AndyDorff\SherpaXML;

use AndyDorff\SherpaXML\Exceptions\FileNotFoundException;
use AndyDorff\SherpaXML\Exceptions\InvalidXMLFileException;
use AndyDorff\SherpaXML\Handler\AbstractHandler;
use AndyDorff\SherpaXML\Handler\Handler;
use AndyDorff\SherpaXML\Handler\HandlerId;
use AndyDorff\SherpaXML\HandlerCollections\InMemoryHandlersCollection;
use AndyDorff\SherpaXML\Interfaces\HandlersCollectionInterface;
use XMLReader;

final class SherpaXML
{
    private XMLReader $xmlReader;
    private HandlersCollectionInterface $handlers;
    /**
     * @var Resolver
     */
    private Resolver $handlerResolver;

    public function __construct(XMLReader $xmlReader)
    {
        $this->xmlReader = $xmlReader;
        $this->handlers = new InMemoryHandlersCollection();
        $this->handlerResolver = new Resolver();
    }

    public function setHandlers(HandlersCollectionInterface $handlers): void
    {
        $this->handlers = $handlers;
    }

    /**
     * @param string $xmlPath
     * @param string|null $encoding
     * @return self
     * @throws InvalidXMLFileException|FileNotFoundException
     */
    public static function open(string $xmlPath, string $encoding = null): self
    {
        if(!file_exists($xmlPath)){
            throw new FileNotFoundException($xmlPath);
        }
        $xmlReader = new XMLReader();
        if(!$xmlReader->open($xmlPath, $encoding)){
            throw new InvalidXMLFileException($xmlPath);
        } else {
            $xmlFile = fopen($xmlPath, "r");
            $checkString = strtolower(fgets($xmlFile));

            if(strpos($checkString, 'xml') === false){
                throw new InvalidXMLFileException($xmlPath);
            }
        }

        return new self($xmlReader);
    }

    public function xmlReader(): XMLReader
    {
        return $this->xmlReader;
    }

    public function handlers(): array
    {
        return $this->handlers->all();
    }

    public function on(string $tagName, $handler): AbstractHandler
    {
        $handler = $this->handlerResolver->resolve($handler);
        $this->handlers->set($tagName, $handler);

        return $handler;
    }

    public function getHandler(string $tagName): ?AbstractHandler
    {
        return $this->handlers->get($tagName);
    }
}
