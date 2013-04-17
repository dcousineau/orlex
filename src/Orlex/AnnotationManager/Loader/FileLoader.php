<?php
namespace Orlex\AnnotationManager\Loader;

use Symfony\Component\Config\Loader\FileLoader as BaseLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\FileLocatorInterface;
/**
 * @see Symfony\Component\Routing\Loader\AnnotationFileLoader
 */
class FileLoader extends BaseLoader {
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var \Closure
     */
    protected $callback;

    /**
     * @param FileLocatorInterface  $locator A FileLocator instance
     *
     * @throws \RuntimeException
     */
    public function __construct(FileLocatorInterface $locator = null)
    {
        if (!$locator) {
            $locator = new FileLocator();
        }

        $this->setCallback(function(){});

        parent::__construct($locator);
    }

    /**
     * @param callable $callback
     */
    public function setCallback(\Closure $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * Loads from annotations from a file.
     *
     * @param string      $file A PHP file path
     * @param string|null $type The resource type
     *
     * @throws \InvalidArgumentException When the file does not exist or its routes cannot be parsed
     */
    public function load($file, $type = null)
    {
        $path = $this->locator->locate($file);

        if ($class = $this->findClass($path)) {
            $callback = $this->callback;
            $callback($class, $path);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'php' === pathinfo($resource, PATHINFO_EXTENSION) && (!$type || 'annotation' === $type);
    }

    /**
     * Returns the full class name for the first class in the file.
     *
     * @param string $file A PHP file path
     *
     * @return string|false Full class name if found, false otherwise
     */
    protected function findClass($file)
    {
        $class = false;
        $namespace = false;
        $tokens = token_get_all(file_get_contents($file));
        for ($i = 0, $count = count($tokens); $i < $count; $i++) {
            $token = $tokens[$i];

            if (!is_array($token)) {
                continue;
            }

            if (true === $class && T_STRING === $token[0]) {
                return $namespace.'\\'.$token[1];
            }

            if (true === $namespace && T_STRING === $token[0]) {
                $namespace = '';
                do {
                    $namespace .= $token[1];
                    $token = $tokens[++$i];
                } while ($i < $count && is_array($token) && in_array($token[0], array(T_NS_SEPARATOR, T_STRING)));
            }

            if (T_CLASS === $token[0]) {
                $class = true;
            }

            if (T_NAMESPACE === $token[0]) {
                $namespace = true;
            }
        }

        return false;
    }
}