<?php

namespace I18n;

use Nette;
use Nette\Neon\Neon;
use Nette\Utils\Arrays;
use Nette\Http\IRequest;

//TODO log untranslated words
//TODO implement count and others parts of string
class Translator implements Nette\Localization\ITranslator
{
    const NEON = '.neon';
    const PHP = '.php';

    private $locale = null;

    private $availableLocales = null;

    private $path = null;

    private $dictionary = null;

    private $ext = '.neon';

    //NOTE arr config parameters as array and get everything from there
    public function __construct($availableLocales = null, $ext = '.neon', IRequest $request = null)
    {
		$this->availableLocales = $availableLocales;
        //extract locale from request if present
		if ($request) {
			$this->getLocaleFromRequest($request, true);
		}

		return $this;
    }

    public function setLocale($locale)
    {
        return $this->locale = $locale;
    }

    public function getAvailableLocales()
    {
        if (!is_array($this->availableLocales)) {
            return [$this->locale];
        }
        return $this->availableLocales;

    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function translate($message, $parameters = null)
    {
        $count = null;
        $params = [];
        $locale = null;

        if (is_integer($parameters)) {
            $count = $parameters;
        } elseif (is_array($parameters)) {
            extract($parameters);
        }
        $locale = $locale ?? ($this->locale ?? $this->setDefaultLocale());

		//if locale is defined
		if ($locale) {
			// weird bugfix from nette forms
			if ((strpos($message, ' ') !== false &&
				(strpos($message, ' ') < strpos($message, '.'))) || strpos($message, '.') === false) {
				return $message;
			}
			// add_prefix for Nette\Validator messages
			if (strpos($message, ' ') > 0) {
				$message = 'forms.'.$message;
			}

			if (!$this->dictionary) {
        		$this->initDictionary(SYSTEM . '/locale', $this->ext);
			}

			return $this->dictionary->getMessage($message, $count, $params, $locale);
		}
		//no translation at all
		return $message;
    }

    public function getLocaleFromRequest(Nette\Http\Request $request = null, $setLocale = false)
    {
		preg_match("/^\/([A-Za-z]{2})\//", $request->getUrl()->getPath(), $localeMatch);
		if (!empty($localeMatch[1])) {
			if (!in_array($localeMatch[1], $this->availableLocales)) {
				throw new \OutOfBoundsException('Locale "'.$localeMatch[1].'" is not supported');
			}
			if (!$setLocale) {

				return $localeMatch[1];

			}
		}
		if ($setLocale) {
			$this->locale = $localeMatch[1] ?? $this->setDefaultLocale();
		}
    }

	private function setDefaultLocale()
	{
		if (is_array($this->availableLocales)) {
			// select FIRST locale in array
			$this->locale = $this->availableLocales[0];
		} elseif (is_string($this->availableLocales && count($this->availableLocales) === 2)) {
			// set locale from string, ie. NON-multilingual application
			$this->locale = $this->availableLocales;
		}
		return $this->locale;
	}

    private function initDictionary($localeDir = null, $ext = '.neon')
    {
        $this->dictionary = new FileDictionary($localeDir, $ext);
    }

}
