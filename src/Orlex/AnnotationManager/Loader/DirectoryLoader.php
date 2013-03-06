<?php
namespace Orlex\AnnotationManager\Loader;

use Symfony\Component\Config\FileLocator;

/**
 * @see Symfony\Component\Routing\Loader\AnnotationDirectoryLoader
 */
class DirectoryLoader extends FileLoader {

    /**
     * Loads from annotations from a directory.
     *
     * @param string      $path A directory path
     * @param string|null $type The resource type
     *
     * @return RouteCollection A RouteCollection instance
     *
     * @throws \InvalidArgumentException When the directory does not exist or its routes cannot be parsed
     */
    public function load($path, $type = null)
    {
        $dir = $this->locator->locate($path);

        $files = iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir), \RecursiveIteratorIterator::LEAVES_ONLY));
        usort($files, function (\SplFileInfo $a, \SplFileInfo $b) {
            return (string) $a > (string) $b ? 1 : -1;
        });

        foreach ($files as $file) {
            /** @var $file \SplFileInfo */
            if ($class = $this->findClass($file)) {
                $callback = $this->callback;
                $callback($class, $file->getPathname());
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        try {
            $path = $this->locator->locate($resource);
        } catch (\Exception $e) {
            return false;
        }

        return is_string($resource) && is_dir($path) && (!$type || 'annotation' === $type);
    }
}