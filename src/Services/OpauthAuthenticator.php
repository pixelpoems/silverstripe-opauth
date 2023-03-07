<?php

namespace Silverstripe\Opauth\Services;


use Opauth;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injector;
use Silverstripe\Opauth\Controllers\OpauthController;
use Silverstripe\Opauth\Forms\OpauthLoginForm;
use SilverStripe\Security\MemberAuthenticator\MemberAuthenticator;

/**
 * Base authenticator for SilverStripe Opauth module.
 * @author Will Morgan <@willmorgan>
 * @author Dan Hensby <@dhensby>
 * @copyright Copyright (c) 2013, Better Brief LLP
 */
class OpauthAuthenticator extends MemberAuthenticator
{
    use Configurable;

    private static
        /**
         * @var Opauth Persistent Opauth instance.
         */
        $opauth;

    /**
     * get_enabled_strategies
     * @return array Enabled strategies set in _config
     */
    public static function get_enabled_strategies(): array
    {
        $strategyConfig = self::config()->opauth_settings['Strategy'];
        return array_keys($strategyConfig);
    }

    /**
     * get_opauth_config
     * @param array $mergeConfig Any extra overrides
     * @return array Config for use with Opauth
     */
    public static function get_opauth_config(array $mergeConfig = array()): array
    {
        $config = self::config();
        return array_merge(
            array(
                'path' => OpauthController::get_path(),
                'callback_url' => OpauthController::get_callback_path(),
            ),
            $config->opauth_settings,
            $mergeConfig
        );
    }

    /**
     * opauth
     * @param boolean $autoRun Should Opauth auto run? Default: false
     * @return Opauth The Opauth instance. Isn't it easy to typo this as Opeth?
     */
    public static function opauth(bool $autoRun = false, $config = array()): Opauth
    {
        if (!isset(self::$opauth)) {
            self::$opauth = new Opauth(self::get_opauth_config($config), $autoRun);
        }
        return self::$opauth;
    }

    /**
     * get_strategy_segment
     * Works around Opauth's weird URL scheme - GoogleStrategy => /google/
     * @return string
     */
    public static function get_strategy_segment($strategy): string
    {
        return preg_replace('/(strategy)$/', '', strtolower($strategy));
    }

    /**
     * @return OpauthLoginForm
     */
    public static function get_login_form(Controller $controller): OpauthLoginForm
    {
        return Injector::inst()->create('OpauthLoginForm', $controller, 'LoginForm');
    }

    /**
     * Get the name of the authentication method
     *
     * @return string Returns the name of the authentication method.
     */
    public static function get_name(): string
    {
        return _t('OpauthAuthenticator.TITLE', 'Social Login');
    }

}
