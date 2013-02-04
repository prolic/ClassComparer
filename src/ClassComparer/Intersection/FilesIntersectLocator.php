<?php
/**
 * Created by JetBrains PhpStorm.
 * @author Oleksandr Khutoretskyy <olekhy@gmail.com>
 *         Date: 2/2/13
 *         Time: 11:28 PM
 */
namespace ClassComparer\Intersection;

use ClassComparer\Scanner\DirectoryScanner;
use Zend\Code\Scanner\FileScanner;

class FilesIntersectLocator implements IntersectAware
{
    /**
     * @var DirectoryScanner
     */
    protected $scanner;

    /**
     * @var string directory where will scanned at first
     */
    protected $firstScanDir;

    /**
     * @var array paths to directories to scan
     */
    protected $entities;

    /**
     * @param array $directories
     * @param $scanner
     */
    public function __construct(array $directories, $scanner)
    {
        $this->setDirectoriesToScan($directories);
        $this->setScanner($scanner);
    }

    /**
     *
     * @return array
     */
    public function getIntersection()
    {
        $entities = $this->getEntities();
        $directory = array_shift($entities);
        $intersection = array();

        foreach ($this->getFiles() as $file)
        {
            $checkFile = $directory . str_replace($this->firstScanDir, '', $file);
            if (!file_exists($checkFile)) {
                continue;
            }

            $intersection[] = array(
                $file,
                $checkFile
            );
        }
        return $intersection;
    }


    /**
     *
     * @return FileScanner[]
     */
    protected function getFiles()
    {
        $scanner = $this->getScanner();
        $scanner->addDirectory($this->firstScanDir);
        return $scanner->getFiles();
    }

    /**
     * @param array $directories
     *
     * @return FilesIntersectLocator
     * @throws Exception\InvalidArgumentException
     */
    public function setEntities(array $directories)
    {
        $error = false;
        if (!empty($directories)) {

            foreach ($directories as $path) {

                if (!is_dir($path))  {
                    $error = true;
                    break;
                }
            }
        }

        if ($error || empty($directories) || count($directories) < 2) {
            throw new Exception\InvalidArgumentException('Invalid directory was provided');
        }
        $this->entities = $directories;
        $this->firstScanDir = array_shift($this->entities);
        return $this;
    }

    /**
     * @return array
     */
    public function getEntities()
    {
        return $this->entities;
    }

    /**
     * @param array $directories
     *
     *
     * @return FilesIntersectLocator
     */
    public function setDirectoriesToScan(array $directories)
    {
        $this->setEntities($directories);
        return $this;
    }


    /**
     * @param DirectoryScanner $directoryScanner
     *
     * @return AbstractMethodsIntersectLocator
     */
    public function setScanner(DirectoryScanner $directoryScanner)
    {
        $this->scanner = $directoryScanner;
        return $this;
    }

    /**
     * @return DirectoryScanner
     */
    public function getScanner()
    {
        return $this->scanner;
    }

}
