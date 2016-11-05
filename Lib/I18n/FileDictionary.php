<?php

namespace I18n;

use Nette;
use Nette\Utils\Arrays;
use Nette\Utils\Strings;
use Nette\Utils\Finder;
use Nette\Neon\Neon;

class FileDictionary implements IDictionary
{
    private $fileExt;
    private $path;
    private $data = [];

    public function __construct($path = '', $ext = null)
    {
        if (!$this->pathExists($path)) {
            throw new PathNotFoundException('Chosen path \''.$path.'\' is not valid');
        }
        if ($ext === null) {
            throw new \InvalidArgumentException('Dictionary extension not defined');
        }
        if ($ext[0] !== '.') {
            $ext = '.'.$ext;
        }
        $this->fileExt = $ext;
        $this->path = $path;
    }

    //ToDo if message is not translated, create it in locale file, or atleast log it somewhere
    public function getMessage($mask, $count = null, $params = [], $locale = null)
    {
        if ($locale === null) {
            throw new \InvalidArgumentException('$locale parameter not set');
        }
        $path = explode('.',$mask);
        if (!isset($this->data[$locale][$path[0]])) {
            $this->addResource($path[0], $locale);
        }
        $message = Arrays::get($this->data, explode('.', $locale.'.'.$mask), null);
//        $message = Arrays::get($this->data, explode('.', $locale.'.'.$mask), null);
        if ($message !== null) {
            return (new Phrase($message)).'';
        }
        return substr($mask, strpos($mask, '.')+1);
    }

    public function addResource($path, $locale)
    {
        if ($locale === null) {
            throw new \InvalidArgumentException('$locale parameter not set');
        }
        $result = Finder::findFiles($path . $this->fileExt)->in($this->path.'/'.$locale. '/');
        if ($result->count() === 0) {
            throw new DictionaryFileException('Dictionary file ' . $path.$this->fileExt . ' not found ('.$this->path.$locale. '/'.$path.')');
        }
        $it = $result->getIterator();
        $it->rewind();

        if ($this->fileExt === '.neon') {
            $content = file_get_contents($it->current());
            $encoder = new Neon;
            $content = $encoder->decode($content);
        }
        if (!isset($this->data[$locale])) {
            $this->data[$locale] = [];
        }


        $this->data[$locale][$path] = $content;
        return true;
    }

    public function getResources()
    {
        return $this->data;
    }

    public function pathExists($path)
    {
        return is_dir($path);
    }
}

class DictionaryFileException extends \Exception
{
}

class PathNotFoundException extends \Exception
{
}
