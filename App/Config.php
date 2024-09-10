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

    // Cookie Config

    public const COOKIE_DEFAULT_EXPIRY = 604800;

    public const COOKIE_USER = "user";
}
