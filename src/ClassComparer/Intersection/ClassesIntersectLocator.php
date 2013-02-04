<?php
/**
 * Created by JetBrains PhpStorm.
 * @author Oleksandr Khutoretskyy <olekhy@gmail.com>
 *         Date: 2/2/13
 *         Time: 11:40 PM
 */
namespace ClassComparer\Intersection;

use Zend\Code\Scanner\FileScanner;
use Exception;
use Zend\Code\Scanner\ClassScanner;


class ClassIntersectLocator implements IntersectAware
{
    /**
     * @var FilesIntersectLocator
     */
    protected $files;

    /**
     * @var array
     */
    protected $entities;

    /**
     * @param $filesScanner
     */
    public function __construct($filesScanner)
    {
        $this->setFiles($filesScanner);

    }

    /**
     * @param array $entities
     *
     * @return mixed
     */
    public function setEntities(array $entities)
    {
        $this->entities = $entities;
        return $this;
    }

    /**
     * @return array
     */
    public function getEntities()
    {
        if (null === $this->entities) {
            $this->setEntities($this->getFiles()->getIntersection());
        }
        return $this->entities;
    }

    /**
     *
     *
     * @throws Exception\RangeException
     * @return array
     */
    function getIntersection()
    {
        $intersection = array();

        foreach ($this->getEntities() as $classFiles) {

            $classScanners = array();
            $cachedClassNames = null;

            foreach ($classFiles as $classFile) {
                $fileScanner = new FileScanner($classFile);

                $classNames = array();
                try {
                    $classNames = $fileScanner->getClassNames();
                } catch (Exception $e)
                {
                    echo    sprintf(
                        'Can not determine class name because file %s contains invalid class',
                        $classFile
                    );
                    echo PHP_EOL;

                }


                if (count($classNames) > 1) {
                    //throw new Exception\RangeException(
                    echo    sprintf(
                            'Can not intersect classes because file %s contains more then one class',
                            $classFile
                        );
                    echo PHP_EOL;
                    //);
                    $cachedClassNames = null;
                    continue;
                }

                if ($cachedClassNames === $classNames || null === $cachedClassNames) {
                    try {
                        $classScanner = $fileScanner->getClasses();
                    } catch (Exception $e)
                    {
                        $cachedClassNames = null;
                        continue;
                    }
                }

                if (!empty($classScanner)) {
                    $classScanners[$classFile] = array_shift($classScanner);
                    $cachedClassNames = $classNames;
                }
            }
            if (!empty($classScanners)) {
                $intersection[] = $classScanners;
            }
        }

        return $intersection;
    }

    /**
     * @param FilesIntersectLocator $files
     *
     * @return ClassIntersectLocator
     */
    public function setFiles(FilesIntersectLocator $files)
    {
        $this->files = $files;
        return $this;
    }

    /**
     * @return FilesIntersectLocator
     */
    public function getFiles()
    {
        return $this->files;
    }
}
