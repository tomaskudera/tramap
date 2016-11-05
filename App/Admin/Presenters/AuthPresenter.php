<?php

namespace Admin\Presenters;

use Core\Components\Forms;
use Nette;
use Nette\Application\UI;
use Nette\Localization\ITranslator;
use Nette\Security\IAuthenticator;

class AuthPresenter extends UI\Presenter
{
	/** @persistent **/
	protected $locale;
	/** @var ITranslator */
	protected $translator;
	/** @var IAuthenticator */
	protected $authenticator;

	protected $recoveryMode = FALSE;

	public function __construct(ITranslator $translator, IAuthenticator $auth)
	{
		$this->translator = $translator;
		$this->authenticator = $auth;
	}

	protected function beforeRender()
	{
		$this->template->locale = $this->translator->getLocale();
		$this->template->setTranslator($this->translator);
		if ($this->getHttpRequest()->getQuery('forgot') !== NULL) {
			$this->recoveryMode = TRUE;
			$this->setView('recover');
		}
	}

	public function createComponentForm()
	{
		$formAction = $this->link('this');
		//in case of returnUrl add to form URL
		$returnUrl = $this->getHttpRequest()->getQuery('returnUrl');
		if ($returnUrl) {
			$formAction.= "?returnUrl=$returnUrl";
		}

		$form = new Forms\Form();
		$form->setAction($formAction);
		$form->addText('login', 'Login');
		$form->addPassword('password', 'Password');
		$form->addSubmitButton('send', 'Submit');
		$form->onSuccess[] = [$this, 'authenticateUser'];
		$form->onSuccess[] = [$this, 'afterLoginRedirect'];
		$form->setTranslator($this->translator);
		return $form;
	}


	/**
	 * Authenticate user
	 * @param Form $form login form
	 */
	public function authenticateUser($form)
	{
		$values = $form->getValues();
		try {
			//NOTE by default set expiration to 7 days
//			$this->user->setExpiration($values->remember ? '14 days' : '20 minutes');
			$this->user->login($values->login, $values->password);
			$this->user->setExpiration('7 days');
		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError($e->getMessage());
		}
	}

	public function afterLoginRedirect()
	{
		$returnUrl = $this->getHttpRequest()->getQuery('returnUrl');
		if ($returnUrl) {
			$this->redirectUrl($returnUrl);
		} else {
			//FUTURE update to custom redirect based on config
			$this->redirect(':Admin:Dashboard:default');
		}
	}

	public function actionLogout()
	{
		$this->user->logout();
		$this->flashMessage('Logout ok', 'success');
		$this->redirect(':Main:Homepage:default');
	}

	//Todo remove on production (:
	/** pure development item */
	public function createComponentRecoveryForm()
	{
		$formAction = $this->link('Auth:recover');
		//in case of returnUrl add to form URL
		$returnUrl = $this->getHttpRequest()->getQuery('returnUrl');
		if ($returnUrl) {
			$formAction.= "?returnUrl=$returnUrl";
		}

		$form = new Forms\Form();
		$form->setAction($formAction);
		$form->addText('login', 'Login');
		$form->addPassword('password', 'Password');
		$form->addPassword('password_check', 'Password again:')
			->setRequired('Fill your password again to check for typo')
			->addRule(Forms\Form::EQUAL, 'Password missmatch', $form['password']);
		$form->onSuccess[] = [$this, 'resetPassword'];
		$form->onSuccess[] = function() {
			$this->redirect('Auth:default');
		};
		$form->setTranslator($this->translator);
		return $form;
	}

	/**
	 * @var Form $form
	 */
	public function resetPassword($form)
	{
		$values = $form->getValues();
		unset($values['password_check']);
		$this->authenticator->resetPassword($values);
	}
}
