<?php

namespace Pixelant\PxaCookieBar\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class CookieUtility
 * @package Pixelant\PxaCookieBar\Utility
 */
class CookieUtility
{

    /**
     * Extension settings
     *
     * @var array
     */
    protected static $settings;

    /**
     * Get plugin settings
     *
     * @return array
     */
    public static function getSettings()
    {
        if (self::$settings === null) {
            self::$settings = GeneralUtility::removeDotsFromTS(
                self::getTSFE()->tmpl->setup['plugin.']['tx_pxacookiebar.']['settings.']
            );
        }

        return self::$settings;
    }

    /**
     * Clear all cookies
     *
     * @return void
     */
    public static function removeAllCookies()
    {
        $cookies = GeneralUtility::trimExplode(';', $_SERVER['HTTP_COOKIE'], true);
        $host = GeneralUtility::getIndpEnv('TYPO3_HOST_ONLY');

        $domainParts = GeneralUtility::trimExplode('.', $host, true);

        $subdomain = $domainParts[0] === 'www' ? substr($host, 3) : ('.' . $host);

        if (count($domainParts) > 2 && $domainParts[0] !== 'www') {
            $domainParts = array_reverse($domainParts);
            $jsDomain = '.' . $domainParts[1] . '.' . $domainParts[0];
        }

        foreach ($cookies as $cookie) {
            $parts = GeneralUtility::trimExplode('=', $cookie, true);

            if ($parts[0] !== 'be_typo_user') {
                setcookie($parts[0], '', time() - 1000);
                setcookie($parts[0], '', time() - 1000, '/');
                setcookie($parts[0], '', time() - 1000, '/', $host);
                setcookie($parts[0], '', time() - 1000, '/', $subdomain);
                if (isset($jsDomain)) {
                    setcookie($parts[0], '', time() - 1000, '/', $jsDomain);
                }
            }
        }

        self::removeFEUserCookie($host, $subdomain);
    }

    /**
     * Remove fe_user_cookie
     *
     * @param string $host
     * @param string $subdomain
     * @return void
     */
    public static function removeFEUserCookie($host, $subdomain)
    {
        setcookie('fe_typo_user', '', time() - 1000);
        setcookie('fe_typo_user', '', time() - 1000, '/');
        setcookie('fe_typo_user', '', time() - 1000, '/', $host);
        setcookie('fe_typo_user', '', time() - 1000, '/', $subdomain);
    }

    /**
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    public static function getTSFE()
    {
        return $GLOBALS['TSFE'];
    }
}
