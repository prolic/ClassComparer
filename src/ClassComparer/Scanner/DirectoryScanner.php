<?php
/**
 * Created by JetBrains PhpStorm.
 * @author Oleksandr Khutoretskyy <olekhy@gmail.com>
 *         Date: 2/4/13
 *         Time: 1:49 PM
 */

namespace ClassComparer\Scanner;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class DirectoryScanner
{
    /**
     * @var bool
     */
    protected $isScanned;

    /**
     * @var
     */
    protected $directories;

    /**
     * @var
     */
    protected $files;

    /**
     * @var array
     */
    protected $blacklist;

    /**
     * @param null|string|array $directory
     * @param null $blacklist
     */
    public function __construct($directory = null, $blacklist = null)
    {
        if ($directory) {
            if (is_string($directory)) {
                $this->addDirectory($directory);
            } elseif (is_array($directory)) {
                foreach ($directory as $d) {
                    $this->addDirectory($d);
                }
            }
        }

        $this->setBlacklist($blacklist);
    }

    /**
     * @param $blacklist
     *
     * @return DirectoryScanner
     */
    public function setBlacklist($blacklist)
    {
        if (!is_array($blacklist)) {
            $this->blacklist = array($blacklist);
        } else {
            $this->blacklist = $blacklist;
        }

        return $this;
    }



    /**
     * @return void
     */
    protected function scan()
    {
        if ($this->isScanned) {
            return;
        }

        // iterate directories creating file scanners
        foreach ($this->directories as $directory) {

            $rdi = new RecursiveDirectoryIterator($directory);
            /** @var $item \SplFileInfo */

            foreach (new RecursiveIteratorIterator($rdi) as $item) {

                $path = $item->getPath();
                $skip = false;
                foreach ($this->getBlacklist() as $pattern)  {

                    if (false !== strpos($path, $pattern)) {

                        $skip = true;
                        break;
                    }


                }

                if (!empty($skip)) {
                    continue;
                }


                if ($item->isFile() && pathinfo($item->getRealPath(), PATHINFO_EXTENSION) == 'php') {
                    $this->files[] = $item->getRealPath();
                }
            }
        }

        $this->isScanned = true;
    }

    /**
     * @param  DirectoryScanner|string $directory
     * @return void
     * @throws \InvalidArgumentException
     */
    public function addDirectory($directory)
    {
        if ($directory instanceof DirectoryScanner) {
            $this->directories[] = $directory;
        } elseif (is_string($directory)) {
            $realDir = realpath($directory);
            if (!$realDir || !is_dir($realDir)) {
                throw new \InvalidArgumentException(sprintf(
                    'Directory "%s" does not exist',
                    $realDir
                ));
            }
            $this->directories[] = $realDir;
        } else {
            throw new \InvalidArgumentException(
                'The argument provided was neither a DirectoryScanner or directory path'
            );
        }
    }

    /**
     * @return array
     */
    protected function getBlacklist()
    {
        return $this->blacklist;
    }

    /**
     * @return mixed
     */
    public function getFiles()
    {
        $this->scan();
        return $this->files;
    }
}
