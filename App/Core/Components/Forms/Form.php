<?php

namespace Core\Components\Forms;

use Core\Components\Forms\Controls;
use Core\Components\Forms\Rendering\TabFormRenderer;
use Nette\Application\UI;

class Form extends UI\Form
{
	public function __construct()
	{
		parent::__construct();
        $this->setRenderer(new TabFormRenderer);
	}

	public function addSubmitButton(string $name, string $caption = NULL)
	{
		return $this[$name] = new Controls\SubmitButton($caption);
	}
}
