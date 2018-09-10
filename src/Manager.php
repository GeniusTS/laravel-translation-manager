<?php

namespace GeniusTS\TranslationManager;


use Illuminate\Support\Str;
use League\Flysystem\Config;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use League\Flysystem\Adapter\Local;
use Illuminate\Support\Facades\Lang;
use GeniusTS\TranslationManager\Exceptions\InvalidNamespaceException;

/**
 * Class Manager
 *
 * @package GeniusTS\TranslationManager
 */
class Manager
{

    /**
     * Translator
     *
     * @var \Illuminate\Translation\Translator
     */
    protected $translator;
    /**
     * Translation loader
     *
     * @var \Illuminate\Translation\LoaderInterface
     */
    protected $loader;

    /**
     * @var \League\Flysystem\Adapter\Local
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $languagesPath;

    /**
     * @var string
     */
    protected $directory_separator;

    /**
     * Manager constructor.
     */
    public function __construct()
    {
        $this->translator = App::make('translator');
        $this->loader = Lang::getLoader();
        $this->languagesPath = App::langPath();
        $this->directory_separator = DIRECTORY_SEPARATOR;
    }

    /**
     * Save a translation file
     *
     * @param array $translation
     * @param       $filename
     * @param       $language
     * @param null  $namespace
     *
     * @return bool
     */
    public function exportFile($translation, $filename, $language, $namespace = null)
    {
        $path = "{$this->languagesPath}{$this->directory_separator}" .
            ($namespace ? "vendor{$this->directory_separator}{$namespace}{$this->directory_separator}" : '') .
            "{$language}{$this->directory_separator}{$filename}.php";

        $content = "<?php \n\n return " . var_export($translation, true) . ';';

        return (bool) $this->filesystem->write($path, $content, new Config);
    }

    /**
     * Get available namespaces
     *
     * @return array
     */
    public function namespaces()
    {
        return array_keys($this->getHints());
    }

    /**
     * Get available groups of a namespace
     *
     * @param null $namespace
     *
     * @return \Illuminate\Support\Collection
     * @throws \GeniusTS\TranslationManager\Exceptions\InvalidNamespaceException
     */
    public function files($namespace = null)
    {
        if (! $namespace)
        {
            $path = $this->namespacePath($this->languagesPath);
        }
        else
        {
            $namespaces = $this->getHints();

            if (! array_key_exists($namespace, $namespaces))
            {
                throw new InvalidNamespaceException("Namespace '{$namespace}' not exist!");
            }

            $path = $this->namespacePath($namespaces[$namespace]);
        }

        $content = $this->pathContent($path);

        return $content
            ->filter(function ($file) {
                return $file['type'] === 'file' && Str::endsWith($file['path'], '.php');
            })
            ->map(function ($file) use ($path) {
                $path = ltrim($path . DIRECTORY_SEPARATOR, '/');

                return $this->groupName(Str::replaceLast($path, '', $file['path']));
            })
            ->flatten();
    }

    /**
     * Get the translation of a group and name space
     *
     * @param string      $file
     * @param string|null $namespace
     * @param string|null $language
     *
     * @return array
     */
    public function translations($file, $namespace = null, $language = null)
    {
        $group = $this->groupName($file);

        $key = ($namespace ? "{$namespace}::" : '') . $group;

        return $this->translator->trans($key, [], $language ?: $this->defaultLanguage());
    }

    /**
     * Get the group name from a filename
     *
     * @param $filename
     *
     * @return mixed
     */
    public function groupName($filename)
    {
        return preg_replace('/\.php$/', '', $filename);
    }

    /**
     * Get default language
     *
     * @return string
     */
    public function defaultLanguage()
    {
        return config('app.fallback_locale', 'en');
    }

    /**
     * Check if loader has namespaces method
     * and create accessor to access hints property
     * if doesn't have the method
     *
     * @return array
     */
    protected function getHints()
    {
        if (method_exists($this->loader, 'namespaces'))
        {
            return $this->loader->namespaces();
        }

        $accessor = \Closure::bind(function () {
            return $this->hints;
        }, $this->loader, $this->loader);

        return $accessor();
    }

    /**
     * Get default language
     *
     * @param string $path
     * @param string $language
     *
     * @return string
     */
    protected function namespacePath($path, $language = null)
    {
        return "{$path}{$this->directory_separator}" . ($language ?: $this->defaultLanguage());
    }

    /**
     * List content of a path
     *
     * @param null $path
     * @param bool $recursive
     *
     * @return \Illuminate\Support\Collection
     */
    protected function pathContent($path = null, $recursive = false)
    {
        return new Collection((new Local($path ?: base_path()))->listContents('', $recursive));
    }

}