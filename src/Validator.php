<?php

namespace LittleNinja;

class Validator
{

    private $rules = array();
    private $errors = array();

    /**
     * @param type $rule
     * @param type $value
     * @param type $params
     * @param type $name
     * @return \LittleNinja\Validator
     */
    public function setRule($rule, $value, $params = null, $name = null, $message = null)
    {
        $this->rules[] = compact('rule', 'value', 'params', 'name', 'message');

        return $this;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        $this->errors = array();
        if (count($this->rules) > 0) {
            foreach ($this->rules as $rule) {
                if (!$this->$rule['rule']($rule['value'], $rule['params'])) {
                    if ($rule['name']) {
                        $this->errors[$rule['name']] = $rule['message'];
                    } else {
                        $config = App::getInstance()->getConfig();
                        $this->errors[$rule['rule']] = $config->messages[$rule['rule']];
                    }
                }
            }
        }

        return (bool) !count($this->errors);
    }

    /**
     *
     * @return Array
     */
    public function errors()
    {
        return $this->errors;
    }

    public function __call($name, $arguments)
    {
        throw new \Exception('Invalid validation rule', 500);
    }

    public static function required($val)
    {
        if (is_array($val)) {
            return !empty($val);
        } else {
            return $val != '';
        }
    }

    public static function matches($val1, $val2)
    {
        return $val1 == $val2;
    }

    public static function matches_strict($val1, $val2)
    {
        return $val1 === $val2;
    }

    public static function different($val1, $val2)
    {
        return $val1 != $val2;
    }

    public static function different_strict($val1, $val2)
    {
        return $val1 !== $val2;
    }

    public static function min_length($value, $lenght)
    {
        return (mb_strlen($value) >= $lenght);
    }

    public static function max_length($value, $lenght)
    {
        return (mb_strlen($value) <= $lenght);
    }

    public static function exact_length($value, $lenght)
    {
        return (mb_strlen($value) == $lenght);
    }

    public static function gt($val1, $val2)
    {
        return ($val1 > $val2);
    }

    public static function lt($val1, $val2)
    {
        return ($val1 < $val2);
    }

    public static function alpha($value)
    {
        return (bool) preg_match('/^([a-z])+$/i', $value);
    }

    public static function alpha_num($value)
    {
        return (bool) preg_match('/^([a-z0-9])+$/i', $value);
    }

    public static function alpha_dash($value)
    {
        return (bool) preg_match('/^([-a-z0-9_-])+$/i', $value);
    }

    public static function numeric($value)
    {
        return is_numeric($value);
    }

    public static function email($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function emails($emails)
    {
        if (is_array($emails)) {
            foreach ($emails as $email) {
                if (!self::email($email)) {
                    return false;
                }
            }
        } else {
            return false;
        }

        return true;
    }

    public static function url($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    public static function ip($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }

    public static function regex($val1, $val2)
    {
        return (bool) preg_match($val2, $val1);
    }

    public static function custom($value, $closure)
    {
        if ($closure instanceof \Closure) {
            return (boolean) call_user_func($closure, $value);
        } else {
            throw new \Exception('Invalid validation function', 500);
        }
    }

}
