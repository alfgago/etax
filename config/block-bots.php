<?php

return [

    /*
     * Enable / disable firewall
     *
     */

    'enabled' => env('BLOCK_BOTS_ENABLED', true),

    'ip_info_key' => env('BLOCK_BOTS_IP_INFO_KEY', null),



    /*
     * Whitelisted  IP addresses
     *      '127.0.0.1',
     */

    'whitelist_ips' => [
        '127.0.0.1',
        '::1'
    ],

    'allow_logged_user' => env('BLOCK_BOTS_ALLOW_LOGGED_USER', true),
    'fake_mode' => env('BLOCK_BOTS_FAKE_MODE', true), // minutes - disabled by default

    /*
     * Log channel
     */
    'channels_info' => [
        'single'
    ],

    /*
     * This Log channel will receive when a bad crawler is detected or someone is banned
     */
    'channels_blocks' => [
        'single'
    ],

    /*
     * Send suspicious events to log?
     *
     */

    'log_blocked_requests' => env('BLOCK_BOTS_LOG_BLOCKED_REQUESTS', true),

    /*
     * The list of allowed user-agents. The value of the key should be a keyword in hostname or * for enable to everyone
     *
     */
    'allowed_crawlers' => [
        'ahrefs' => 'ahrefs',
        'alexa' => 'alexa',
        'ask' => 'ask',
        'baidu' => 'baidu',
        'bing' => 'msn.com',
        'duckduck' => '*',
        'exabot' => 'exabot',
        'facebook' => 'facebook',
        'google' => 'google',
        'msn' => 'msn',
        'msnbot' => 'msn.com',
        'sogou' => 'sogou',
        'soso' => 'soso',
        'twitter' => 'twitter',
        'yahoo' => 'yahoo',
        'yandex' => 'yandex',
    ],
];

