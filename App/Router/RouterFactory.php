<?php

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;


class RouterFactory
{
	use Nette\StaticClass;

	/** @var App\Libs\I18n\Translator */
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
		$router[] = new Route('[<locale='.$default.' '.$options.'>/]admin/<presenter>/[<action>/][<id>]', [
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
		$router[] = new Route('[<locale='.$default.' '.$options.'>/]<presenter>/<action>/[<id>]', [
//        $router[] = new Route('[<locale=cs cs|en>/]<presenter>/<action>/[<id>]', [
			'module' => 'Front',
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

//		$router[] = new Route('[<locale=cs cs|en>/]<presenter>/<action>/[<id>]', [
//            'locale' => 'cs',
//            'presenter' => 'Homepage',
//            'action' => 'default',
//            'id' => null,
//        ]);
		return $router;
	}

	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter()
	{
		$router = new RouteList;
		$router[] = new SilentTest('[<locale=cs cs|en>/]<presenter>/<action>/[<id>]', [
			'presenter' => 'Homepage',
			'action' => 'default',
			'locale' => 'cs',
			null => [
				Route::FILTER_OUT => function($params) {
				return $params;
			}]
		]);
//		$router[] = new Route('[<locale=cs cs|en>/]<presenter>/<action>/[<id>]', [
//            'locale' => 'cs',
//            'presenter' => 'Homepage',
//            'action' => 'default',
//            'id' => null,
//        ]);
		return $router;
	}

}

class SilentTest extends Route
{
}
