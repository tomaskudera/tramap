<?php

namespace Core\Components\Forms\Controls;

use Nette\Forms\Controls;
use Nette\Utils\Html;

class SubmitButton extends Controls\SubmitButton
{
	public function __construct($caption = NULL)
	{
		parent::__construct($caption);
		$this->control = Html::el('button', ['type' => 'submit', 'name' => NULL]);
		$this->control->setHtml($caption);
	}
}
