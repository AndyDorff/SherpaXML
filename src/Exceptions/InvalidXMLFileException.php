<?php


namespace AndyDorff\SherpaXML\Exceptions;


use Throwable;

final class InvalidXMLFileException extends \Exception
{
    private string $xmlPath;
    private int $invalidLine = -1;

    public function __construct(string $xmlPath, int $code = 0, Throwable $previous = null)
    {
        $message = 'The file "'.$xmlPath.'" is an invalid xml file';
        parent::__construct($message, $code, $previous);

        $this->xmlPath = $xmlPath;
    }

    public function onLine(int $line): self
    {
        $this->message = 'The file "'.$this->xmlPath.'" is an invalid on '.$line.' line';
        $this->invalidLine = $line;

        return $this;
    }

    public function getInvalidLine(): int
    {
        return $this->invalidLine;
    }

    public function getXmlPath(): string
    {
        return $this->xmlPath;
    }
}