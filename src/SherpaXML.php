<?php

namespace AndyDorff\SherpaXML;

use AndyDorff\SherpaXML\Exceptions\FileNotFoundException;
use AndyDorff\SherpaXML\Exceptions\InvalidXMLFileException;
use AndyDorff\SherpaXML\Handler\AbstractClosureHandler;
use AndyDorff\SherpaXML\HandlerCollections\InMemoryHandlersCollection;
use AndyDorff\SherpaXML\Interfaces\HandlersCollectionInterface;
use XMLReader;

final class SherpaXML implements \Iterator
{
    private XMLReader $xmlReader;
    private HandlersCollectionInterface $handlers;
    /**
     * @var Resolver
     */
    private Resolver $handlerResolver;

    private int $index = 0;
    private ?bool $lastRead = null;

    private array $elementsStack = [];

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

    public function on(string $tagPath, $handler): AbstractClosureHandler
    {
        $tagPath = $this->getCurrentPath($tagPath);

        $handler = $this->handlerResolver->resolve($handler);
        $this->handlers->set($tagPath, $handler);

        return $handler;
    }

    public function extractHandler(string $tagPath): ?AbstractClosureHandler
    {
        $tagPath = $this->normalizeTagPath($tagPath);
        if($handler = $this->handlers->get($tagPath)){
            $this->handlers->remove($tagPath);
        }

        return $handler;
    }

    public function getHandler(string $tagPath): ?AbstractClosureHandler
    {
        $tagPath = $this->normalizeTagPath($tagPath);

        return $this->handlers->get($tagPath);
    }

    private function normalizeTagPath(string $tagPath)
    {
        if(substr($tagPath, 0, 1) !== '/'){
            $tagPath = $this->getCurrentPath($tagPath);
        }

        return $tagPath;
    }

    public function moveToEnd()
    {
        if($this->valid() === null){
            $this->next();
        }
        while($this->valid()){
            $this->next();
        }
    }

    public function moveToNextElement(): bool
    {
        return $this->moveToNextNodeByType(XMLReader::ELEMENT);
    }

    public function moveToNextNodeByType(int $nodeType): bool
    {
        if($this->valid() === null) {
            $this->rewind();
        } elseif ($this->valid()) {
            $this->next();
        }

        while($this->valid()) {
            if($this->xmlReader->nodeType === $nodeType){
                break;
            }
            $this->next();
        }

        return $this->valid();
    }

    /**
     * @return XMLReader
     */
    public function current()
    {
        return $this->xmlReader;
    }

    public function next()
    {
        if(
            ($this->lastRead = $this->xmlReader->read())
            && $this->xmlReader->nodeType === XMLReader::ELEMENT
        ) {
            $depth = $this->xmlReader->depth;
            $this->elementsStack[$depth] = $this->getCurrentElementInfo();
            if(count($this->elementsStack) !== $depth + 1){
                $this->elementsStack = array_slice($this->elementsStack, 0, $depth + 1);
            }
        }

        $this->index++;
    }

    public function getCurrentNodeType(): int
    {
        return $this->xmlReader->nodeType;
    }

    public function getCurrentElementInfo(): array
    {
        return [
            'name' => $this->xmlReader->name,
            'attributes' => $this->extractAttributes($this->current())
        ];
    }

    private function extractAttributes(XMLReader $reader): array
    {
        $result = [];
        while($reader->moveToNextAttribute()){
            $result[$reader->name] = $reader->value;
        }
        $reader->moveToElement();

        return $result;
    }

    public function key()
    {
        return $this->index;
    }

    public function valid()
    {
        return $this->lastRead;
    }

    public function rewind()
    {
        if($this->xmlReader->nodeType === XMLReader::NONE){
            $this->next();
        } elseif ($this->lastRead === null) {
            $this->lastRead = true;
        }
        $this->index = 0;
    }

    public function getCurrentPath(string $path = null): string
    {
        $elementsStack = $this->elementsStack;
        if($path && ($name = rtrim($path, '/'))){
            $elementsStack[] = ['name' => $name];
        }

        return '/'.implode('/', array_map(function(array $elementData){
            return $elementData['name'];
        }, $elementsStack));

    }
}
