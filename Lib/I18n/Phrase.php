<?php

namespace I18n;

class Phrase implements IMessage
{

    private $message;

    public function __construct($message, $count = null, $parameters = null)
    {
        $this->message = $message;
    }

    public function replacePlaceholders(array $replacements)
    {
        // TODO implement
    }

    public function __toString()
    {
        return $this->message;
    }

}
