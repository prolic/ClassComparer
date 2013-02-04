<?php
/**
 * Created by JetBrains PhpStorm.
 * @author Oleksandr Khutoretskyy <olekhy@gmail.com>
 *         Date: 2/2/13
 *         Time: 11:59 PM
 */
namespace Compare\Difference\Result;

class Methods
{
    protected $className;
    protected $methodName;
    protected $path1;
    protected $path2;
    protected $methodsDiff;
    protected $paramsDiff;

    public function __construct($className, $method, $methodDiff,
                                $paramsDiff, $path1, $path2
    ) {
        $this->setMethodsDiff($methodDiff);
        $this->setParamsDiff($paramsDiff);
        $this->setClassName($className);
        $this->setMethodName($method);
        $this->setPath1($path1);
        $this->setPath2($path2);
    }

    public function setClassName($className)
    {
        $this->className = $className;
    }

    public function getClassName()
    {
        return $this->className;
    }

    public function setMethodName($methodName)
    {
        $this->methodName = $methodName;
    }

    public function getMethodName()
    {
        return $this->methodName;
    }

    public function getMethodNameFull()
    {
        return sprintf('%s::%s', $this->getClassName(), $this->getMethodName());
    }
    public function setMethodsDiff(array $methodsDiff)
    {
        $this->methodsDiff = $methodsDiff;
    }

    public function getMethodsDiff()
    {
        return $this->methodsDiff;
    }

    public function setParamsDiff($paramsDiff)
    {
        $this->paramsDiff = $paramsDiff;
    }

    public function getParamsDiff()
    {
        return $this->paramsDiff;
    }

    public function setPath1($path1)
    {
        $this->path1 = $path1;
    }

    public function getPath1()
    {
        return $this->path1;
    }

    public function setPath2($path2)
    {
        $this->path2 = $path2;
    }

    public function getPath2()
    {
        return $this->path2;
    }

    public function __toString()
    {

        $methodInfo = '';
        $paramsInfo = '';
        $format =<<<OUT
Deference found in %s.
 %s
 %s
OUT;
        $paths = array(
            $this->getPath1(),
            $this->getPath2()
        );
        foreach ($this->getMethodsDiff() as $n => $md) {
            if (!empty($md)) {
                $methodInfo = sprintf('info: %s in path: %s', join(', ', $md), $paths[$n]);
            }
        }

        foreach ($this->getParamsDiff() as $n => $pd) {
            if (!empty($pd)) {
                $paramsInfo = sprintf('info: %s in path: %s', join(', ', $pd), $paths[$n]);
            }
        }

        return sprintf($format .PHP_EOL, $this->getMethodNameFull(), $methodInfo, $paramsInfo);
    }
}
