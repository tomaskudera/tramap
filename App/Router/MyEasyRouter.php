<?php

namespace Router;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;


class MyEasyRouter
{
	use Nette\StaticClass;

	/** @var I18n\Translator */
	private $translator;

	public function __construct(Nette\Localization\ITranslator $translator)
	{
		$this->translator = $translator;

	}

	/**
	 * @return Nette\Application\IRouter
	 */
	public function create()
	{
		$options = $this->translator->getAvailableLocales();
		$default = $options[0];
		$options = implode('|', $options);

		$router = new RouteList;

		/**
		 * NOTE - admin app only
		 * if condition is true, set application to admin mode
		 */
		if (ADMIN_APP) {
			$router[] = new Route('[<locale='.$default.' '.$options.'>/]<presenter>/[<action>/][<id>]', [
				'module' => 'Admin',
				'presenter' => 'Dashboard',
				'action' => 'default',
				'locale' => $this->translator->getLocale(),
				'id' => null,
				null => [
					Route::FILTER_OUT => function($params) {
						if (!isset($params['locale'])) {
							$params['locale'] = $this->translator->getLocale();
						}
						//todo translation
					return $params;
				}]
			]);
		}
		$router[] = new Route('[<locale='.$default.' '.$options.'>/]<presenter>/[<action>/][<id>]', [
			'module' => 'Main',
			'presenter' => 'Homepage',
			'action' => 'default',
			'locale' => $this->translator->getLocale(),
			'id' => null,
			null => [
				Route::FILTER_OUT => function($params) {
					if (!isset($params['locale'])) {
						$params['locale'] = $this->translator->getLocale();
					}
					//todo translation
				return $params;
			}]
		]);

		return $router;
	}
}
