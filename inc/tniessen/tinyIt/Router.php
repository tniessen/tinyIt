<?php
namespace tniessen\tinyIt;

class Router
{
    private $reqUrl;
    private $pathElements;
    private $params;

    public function __construct($reqUrl, $params)
    {
        if($reqUrl instanceof URL) {
            $this->reqUrl = $reqUrl;
        } else {
            $this->reqUrl = URL::parse($reqUrl);
        }
        $this->params = $params;
    }

    public function getPathElements()
    {
        if($this->pathElements == null) {
            $reqPath = $this->reqUrl->path;
            if($reqPath && $reqPath[0] === '/') {
                $reqPath = substr($reqPath, 1);
            }
            $this->pathElements = explode('/', $reqPath);
        }
        return $this->pathElements;
    }

    public function getParameters()
    {
        return $this->params;
    }

    public function match($exp)
    {
        $parts = explode('/', $exp);
        $paths = $this->getPathElements();
        $nparts = count($parts);

        $matched = array();
        $allConsumed = false;

        for($i = 0; $i < $nparts && !$allConsumed; $i++) {
            $part = $parts[$i];
            if(substr($part, -1) === '?') {
                if($i >= count($paths)) {
                    break;
                }
                $part = substr($part, 0, -1);
            } else {
                if($i >= count($paths)) {
                    return false;
                }
            }
            if($part === '%') {
                $matched[] = $paths[$i];
            } else if($part === '%%') {
                $str = implode('/', array_slice($paths, $i));
                $matched[] = $str;
                $allConsumed = true;
            } else if($part === '%%[]') {
                $matched[] = array_slice($paths, $i);
                $allConsumed = true;
            } else {
                if($paths[$i] !== $part) {
                    return false;
                }
            }
        }
        if($i < count($paths) - 1 && !$allConsumed) {
            return false;
        }
        return $matched;
    }

    public static function fromGeneratedURL($url, $reqUrlKey)
    {
        $params = $url->parseQuery();
        $reqUrl = $params[$reqUrlKey];
        unset($params[$reqUrlKey]);
        return new Router($reqUrl, $params);
    }

}
