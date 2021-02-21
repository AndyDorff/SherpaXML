<?php


namespace AndyDorff\SherpaXML\Handler;


class Handler
{
    /**
     * @var HandlerId
     */
    private HandlerId $id;
    /**
     * @var callable|null
     */
    private $handle;

    public function __construct(HandlerId $id, callable $handle = null)
    {
        $this->id = $id;
        if($handle){
            $this->delegate($handle);
        }
    }

    public function delegate(callable $handle): void
    {
        $this->handle = $handle;
    }

    public function id(): HandlerId
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function __invoke()
    {
        return call_user_func($this->handle, $this);
    }
}