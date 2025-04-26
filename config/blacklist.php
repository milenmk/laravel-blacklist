<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Blacklist Mode
    |--------------------------------------------------------------------------
    |
    | This setting determines which word lists to use when validating user input.
    | Options:
    | - 'blacklist': Only use the system blacklist words
    | - 'profanity': Only use the profanity/offensive words
    | - 'both': Use both blacklist and profanity words
    |
    */

    'mode' => 'blacklist', // Options: 'blacklist', 'profanity', 'both'

    /*
    |--------------------------------------------------------------------------
    | System Blacklist
    |--------------------------------------------------------------------------
    |
    | This array contains system-related words that should be blacklisted when validating
    | user input. Any field containing these words will be rejected.
    | These are typically used to prevent username squatting or system impersonation.
    |
    */
    'blacklist' => [
        'system',
        'god',
        'super',
        'abuse',
        'account',
        'adm',
        'admin',
        'admins',
        'administrator',
        'administrators',
        'all',
        'ceo',
        'cfo',
        'contact',
        'coo',
        'customer',
        'document',
        'download',
        'faq',
        'file',
        'files',
        'ftp',
        'help',
        'home',
        'host',
        'http',
        'https',
        'imap',
        'info',
        'ldap',
        'list',
        'majordomo',
        'manager',
        'marketing',
        'master',
        'member',
        'membership',
        'mis',
        'news',
        'noreply',
        'office',
        'owner',
        'password',
        'pop',
        'postfix',
        'postmaster',
        'register',
        'registration',
        'root',
        'sales',
        'secure',
        'security',
        'sftp',
        'shop',
        'smtp',
        'ssl',
        'support',
        'sysadmin',
        'system',
        'test',
        'trouble',
        'usenet',
        'user',
        'web',
        'webserver',
        'wheel',
        'vww',
        'wvw',
        'wwv',
        'www',
        'www-data',
    ],

    /*
    |--------------------------------------------------------------------------
    | Profanity List
    |--------------------------------------------------------------------------
    |
    | This array contains profanity, curse words, and offensive terms that should
    | be blacklisted when validating user input. Any field containing these words
    | will be rejected.
    |
    */
    'profanity' => [
        // Common profanity
        'ass',
        'asshole',
        'bastard',
        'bitch',
        'bullshit',
        'crap',
        'damn',
        'dick',
        'douchebag',
        'fuck',
        'fucking',
        'jackass',
        'moron',
        'piss',
        'shit',
        'whore',

        // Offensive slurs and terms
        'retard',
        'slut',
        'idiot',
        'stupid',
        'dumb',
        'loser',
        'jerk',

        // Internet slang/jargon
        'wtf',
        'stfu',
        'gtfo',
        'ffs',
        'lmao',
        'lmfao',
        'omfg',
        'af',
        'bs',

        // Mild profanity
        'hell',
        'heck',
        'darn',
        'suck',
        'sucker',

        // Euphemisms
        'frick',
        'freaking',
        'effing',
        'wth',
        'omg',

        // Body parts used offensively
        'boob',
        'tit',
        'cock',
        'penis',
        'vagina',

        // Insults
        'noob',
        'troll',
        'pathetic',
        'worthless',
        'failure',
        'scumbag',
    ],
];
