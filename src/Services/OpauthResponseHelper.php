<?php

namespace Silverstripe\Opauth\Services;


use SilverStripe\i18n\i18n;

/**
 * OpauthResponseHelper
 * Utility class for handling responses from Opauth.
 * Used in parsing. Can and should be referenced in _config.yml for helping
 * parse things for member_mapping.
 * @author Will Morgan <@willmorgan>
 * @copyright Copyright (c) 2013, Better Brief LLP
 */
class OpauthResponseHelper
{

    /**
     * Take the first part of the name
     * @return string
     */
    public static function get_first_name($source): string
    {
        $name = explode(' ', self::parse_source_path('info.name', $source));
        return array_shift($name);
    }

    /**
     * Take all but the first part of the name
     * @return string
     */
    public static function get_last_name($source): string
    {
        $name = explode(' ', self::parse_source_path('info.name', $source));
        array_shift($name);
        return join(' ', $name);
    }

    /**
     * Twitter responds with just a language (also a TZ, but unused for now)
     * If the PECL Locale extension is used it may be possible to combine both
     * the TZ and the language to fine tune a user's location, but a bit OTT.
     * @return string
     */
    public static function get_twitter_locale($source): string
    {
        $language = self::parse_source_path('raw.lang', $source);
        return self::get_smart_locale($language);
    }

    /**
     * Google responds near perfectly for locales, if populated.
     * Fallback otherwise.
     * @return string
     */
    public static function get_google_locale($source): string
    {
        $locale = self::parse_source_path('raw.locale', $source);
        if (!$locale) {
            return self::get_smart_locale();
        }
        return str_replace('-', '_', $locale);
    }

    /**
     * Try very hard to get a locale for this user. Helps for i18n etc.
     * @param null $language
     * @return string
     */
    public static function get_smart_locale($language = null): string
    {
        return i18n::get_locale();
    }

    /**
     * Dot notation parser. Looks for an index or fails gracefully if not found.
     * @param string $path The path, dot notated.
     * @param array $source The source in which to search.
     * @return array|string|null
     */
    public static function parse_source_path($path, $source): array|string|null
    {
        $fragments = explode('.', $path);
        $currentFrame = $source;
        foreach ($fragments as $fragment) {
            if (!isset($currentFrame[$fragment])) {
                return null;
            }
            $currentFrame = $currentFrame[$fragment];
        }
        return $currentFrame;
    }

}
