<?php

namespace App;

/**
 * Application configuration
 *
 * PHP version 7.0
 */
class Config
{
    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    const SHOW_ERRORS = true;

    /**
     * The remember cookie name.
     * @var string
     */
    public const REMEMBER_COOKIE_NAME = "user";

    /**
     * The remember cookie expiry time.
     * @var integer
     */
    public const REMEMBER_COOKIE_EXPIRY = 60 * 60 * 24 * 30;
}
