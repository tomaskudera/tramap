<?php

namespace Main\Presenters;

use Nette;
use Core\Components\Navigation\NavigationControl;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

	/** $persistent */
	protected $locale;

	/** $var Nette\Localization\ITranslator */
	protected $translator;

	public function injectTranslator(Nette\Localization\ITranslator $tr)
	{
		$this->translator = $tr;
		$this->locale = $tr->getLocale();
	}

	public function getTranslator()
	{
		return $this->translator;
	}

	public function beforeRender()
	{
		$this->template->locale = $this->translator->getLocale();
		$this->template->setTranslator($this->translator);
	}

	public function createComponentNavigation()
	{
		$presenter = new \ReflectionClass($this);
//		//load module specific config
		$navigationFile = dirname($presenter->getFilename()) . '/../config/navigation.php';
		if (is_readable($navigationFile)) {
			$nav = include $navigationFile;
		}
//		$items = $this->facade->getMenuItems($this->locale);
//		$nav = [];
//		foreach($items as $item) {
//			$nav[$item->headline] = $item->url;
//		}
		$control = new NavigationControl($nav);

		return $control;
	}
}
