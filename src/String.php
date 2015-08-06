<?php

namespace LittleNinja;

class String {

    public static function sanitize($value) {
        $value = trim($value);
        $sanitized = strip_tags(
                $value, '<a><strong><em><ul><ol><li><pre><sup><sub><code><blockquote><h2><h3><h4><h5><h6>'
        );

        return $sanitized;
    }

    public static function limit($value, $limit = 100, $end = '...') {
        if (mb_strlen($value) <= $limit) {
            return $value;
        }

        return rtrim(mb_substr($value, 0, $limit, 'UTF-8')) . $end;
    }

}
