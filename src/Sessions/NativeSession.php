<?php

namespace LittleNinja\Sessions;

class NativeSession implements ISession
{

    public function __construct($name, $lifetime = 3600, $path = null, $domain = null, $secure = false)
    {
        if (strlen($name) < 1) {
            $name = '_sess';
        }

        session_name($name);
        session_set_cookie_params($lifetime, $path, $domain, $secure, true);
        session_start();
    }

    public function getSessionId()
    {
        session_id();
    }

    public function saveSession()
    {
        session_write_close();
    }

    public function destroySession()
    {
        session_destroy();
    }

    public function __get($key)
    {
        return $_SESSION[$key];
    }

    public function __set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

}
