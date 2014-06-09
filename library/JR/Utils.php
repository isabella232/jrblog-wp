<?php

namespace JR;

use \Michelf\MarkdownExtra;
use \Michelf\SmartyPants;

class Utils {

    /**
     * Parses markdown and optionally fixes punctuation with SmartyPants
     * 
     * @param  string  $content         String to be parsed
     * @param  boolean $fix_punctuation Pass 'false' punctionation should not be fixed
     * @return string                   Parsed string
     */
    public static function parseMarkdown($content, $fix_punctuation = true)
    {
        if (!is_string($content)) return null;

        $content = MarkdownExtra::defaultTransform(do_shortcode($content));

        if ($fix_punctuation)
            $content = self::fixPunctuation($content);

        return $content;
    }

    /**
     * Fixes punctuation with SmartyPants
     * 
     * @param  string $content String to be parsed
     * @return string          Parsed string
     */
    public static function fixPunctuation($content)
    {
        if (!is_string($content)) return null;

        return SmartyPants::defaultTransform($content);
    }

    /**
     * Returns ID of the highest level ancestor for the target entry ID
     * 
     * @param  int  $id  ID of the target entry
     * @return int       ID of the top parent entry
     */
    public static function getTopParent($id)
    {
        if (!$id || intval($id) == 0) return null;

        $ancestors = get_post_ancestors($id);

        if (!$ancestors) return null;

        return $ancestors[count($ancestors) - 1];
    }

    /**
     * Checks if a variable contains valid JSON
     * 
     * @param  string  $value String to be checked
     * @return boolean        True if the string is JSON, false if not
     */
    public static function isJson($value)
    {
        json_decode($value);

        return ((preg_match('/^\[/', $value) || preg_match('/^{/', $value)) && json_last_error() == JSON_ERROR_NONE) ? true : false;
    }
}