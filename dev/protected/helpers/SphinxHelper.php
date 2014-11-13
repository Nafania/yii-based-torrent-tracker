<?php

/**
 * Created by PhpStorm.
 * User: ikazdym
 * Date: 13.11.2014
 * Time: 18:48
 */
class SphinxHelper
{
    public static function escapeMatch($string)
    {
        $from = ['\\', '(', ')', '|', '-', '!', '@', '~', '"', '&', '/', '^', '$', '='];
        $to = ['\\\\', '\(', '\)', '\|', '\-', '\!', '\@', '\~', '\"', '\&', '\/', '\^', '\$', '\='];
        return str_replace($from, $to, $string);
    }
}