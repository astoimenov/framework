<?php

namespace LittleNinja;

use LittleNinja\Support\Str;

class Request
{
    private static $instance = null;
    private $get = [];
    private $post = [];
    private $cookies = [];

    private function __construct()
    {
        $this->cookies = $_COOKIE;
    }

    public function setGet($get)
    {
        if (is_array($get)) {
            $this->get = $get;
        }

        return $this;
    }

    public function setPost($post)
    {
        if (is_array($post)) {
            $this->post = $post;
        }

        return $this;
    }

    public function hasGet($id)
    {
        return array_key_exists($id, $this->get);
    }

    public function hasPost($name)
    {
        return array_key_exists($name, $this->post);
    }

    public function hasCookies($name)
    {
        return array_key_exists($name, $this->cookies);
    }

    public function get($id, $normalize = null, $default = null)
    {
        if ($this->hasGet($id)) {
            if ($normalize !== null) {
                return Str::normalize($this->get[$id], $normalize);
            }

            return $this->get[$id];
        }

        return $default;
    }

    public function post($name, $normalize = null, $default = null)
    {
        if ($this->hasPost($name)) {
            if ($normalize !== null) {
                return Str::normalize($this->post[$name], $normalize);
            }

            return $this->post[$name];
        }

        return $default;
    }

    public function cookies($name, $normalize = null, $default = null)
    {
        if ($this->hasCookies($name)) {
            if ($normalize !== null) {
                return Str::normalize($this->cookies[$name], $normalize);
            }

            return $this->cookies[$name];
        }

        return $default;
    }

    /**
     * @return \LittleNinja\InputData
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
