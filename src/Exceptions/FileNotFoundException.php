<?php


namespace AndyDorff\SherpaXML\Exceptions;


use Throwable;

final class FileNotFoundException extends \Exception
{
    private string $filePath;

    public function __construct(string $filePath, int $code = 0, Throwable $previous = null)
    {
        $message = 'The file "'.$filePath.'" not found';
        parent::__construct($message, $code, $previous);

        $this->filePath = $filePath;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }
}