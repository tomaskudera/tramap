<?php

namespace Core\Components\Navigation;

use Nette;

class NavigationControl extends Nette\Application\UI\Control
{
	/**
	 * navigation nodes
	 */
	protected $nodes;
	/**
	 * template view
	 */
	protected $view;

	/**
	 * Navigation Constructor
	 * @private
	 * @param array  [$nodes             = []]       array of nodes
	 * @param string [$view              = 'default'] view filename
	 */
	public function __construct($nodes = [], $view = 'default')
	{
		$this->nodes = $nodes;
		$this->view = strpos($view, '.latte') === FALSE ? $view . '.latte' : $view;
	}

	/**
	 * Render method
	 */
	public function render()
	{
		$this->template->nodes = $this->nodes;
		$this->template->setFile(__DIR__ . '/templates/' . $this->view);
		$this->template->render();
	}
}
