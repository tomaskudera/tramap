<?php

namespace Admin\Presenters;

use Core\Components\Navigation\NavigationControl;
use Nette;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

    /** $persistent */
    protected $locale;

    /** $var Nette\Localization\ITranslator */
    protected $translator;

	/**
	 * Check for right access rights and if necessary, redirect to login with appropriate return link
	 */
	protected function startup()
	{
		if (!$this->user->isLoggedIn()) {
			//prevent :Admin:Dashboard:default from adding to returnUrl
			if ($this->request->getPresenterName() == 'Admin:Dashboard' && $this->request->getParameter('action') == 'default') {
				$this->redirect("Auth:default", []);
			} else {
				//add returnUrl 
				$this->redirect("Auth:default", ['returnUrl' => $this->link('this')]);
			}
			
		}
		parent::startup();
	}

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

	//TODO create admin menu according to user role
	public function createComponentNavigation()
	{
		$userRole = $this->user->getRoles()[0];	//select first (the only one) role

		$presenter = new \ReflectionClass($this);
		//load module specific config
		$navigationFile = dirname($presenter->getFilename()) . '/../config/navigation.php';
		if (is_readable($navigationFile)) {
			$nav = include $navigationFile;
			$control = new NavigationControl($nav[$userRole]);
		}
		return $control;
	}
}
