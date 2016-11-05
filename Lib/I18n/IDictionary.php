<?php

namespace I18n;

interface IDictionary
{
    /**
     * @return I18n\IMessage
     */
    public function getMessage($mask, $count = null, $params = [], $locale = null);
}
