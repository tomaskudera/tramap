<?php

namespace I18n;

interface IMessage
{
    public function replacePlaceholders(array $values);
}
