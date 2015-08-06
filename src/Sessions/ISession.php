<?php

namespace LittleNinja\Sessions;

interface ISession {

    public function getSessionId();

    public function saveSession();

    public function destroySession();

    public function __get($key);

    public function __set($key, $value);
}
