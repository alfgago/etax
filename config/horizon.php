<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Horizon Domain
    |--------------------------------------------------------------------------
    |
    | This is the subdomain where Horizon will be accessible from. If this
    | setting is null, Horizon will reside under the same domain as the
    | application. Otherwise, this value will serve as the subdomain.
    |
    */

    'domain' => null,

    /*
    |--------------------------------------------------------------------------
    | Horizon Path
    |--------------------------------------------------------------------------
    |
    | This is the URI path where Horizon will be accessible from. Feel free
    | to change this path to anything you like. Note that the URI will not
    | affect the paths of its internal API that aren't exposed to users.
    |
    */

    'path' => 'horizon',

    /*
    |--------------------------------------------------------------------------
    | Horizon Redis Connection
    |--------------------------------------------------------------------------
    |
    | This is the name of the Redis connection where Horizon will store the
    | meta information required for it to function. It includes the list
    | of supervisors, failed jobs, job metrics, and other information.
    |
    */

    'use' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Horizon Redis Prefix
    |--------------------------------------------------------------------------
    |
    | This prefix will be used when storing all Horizon data in Redis. You
    | may modify the prefix when you are running multiple installations
    | of Horizon on the same server so that they don't have problems.
    |
    */

    'prefix' => env('HORIZON_PREFIX', 'horizon:'),

    /*
    |--------------------------------------------------------------------------
    | Horizon Route Middleware
    |--------------------------------------------------------------------------
    |
    | These middleware will get attached onto each Horizon route, giving you
    | the chance to add your own middleware to this list or change any of
    | the existing middleware. Or, you can simply stick with this list.
    |
    */

    'middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Queue Wait Time Thresholds
    |--------------------------------------------------------------------------
    |
    | This option allows you to configure when the LongWaitDetected event
    | will be fired. Every connection / queue combination may have its
    | own, unique threshold (in seconds) before this event is fired.
    |
    */

    'waits' => [
        'redis:default' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Job Trimming Times
    |--------------------------------------------------------------------------
    |
    | Here you can configure for how long (in minutes) you desire Horizon to
    | persist the recent and failed jobs. Typically, recent jobs are kept
    | for one hour while all failed jobs are stored for an entire week.
    |
    */

    'trim' => [
        'recent' => 60,
        'failed' => 10080,
        'monitored' => 10080,
    ],

    /*
    |--------------------------------------------------------------------------
    | Fast Termination
    |--------------------------------------------------------------------------
    |
    | When this option is enabled, Horizon's "terminate" command will not
    | wait on all of the workers to terminate unless the --wait option
    | is provided. Fast termination can shorten deployment delay by
    | allowing a new instance of Horizon to start while the last
    | instance will continue to terminate each of its workers.
    |
    */

    'fast_termination' => false,

    /*
    |--------------------------------------------------------------------------
    | Memory Limit (MB)
    |--------------------------------------------------------------------------
    |
    | This value describes the maximum amount of memory the Horizon worker
    | may consume before it is terminated and restarted. You should set
    | this value according to the resources available to your server.
    |
    */

    'memory_limit' => 512,

    /*
    |--------------------------------------------------------------------------
    | Queue Worker Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may define the queue worker settings used by your application
    | in all environments. These supervisors and settings handle all your
    | queued jobs and will be provisioned by Horizon during deployment.
    |
    */

    'environments' => [
        'production' => [
            'default-supervisor' => [
                'connection' => 'redis',
                'queue' => ['default'],
                'balance' => 'auto',
                'processes' => 2,
                'minProcesses' => 2,
                'maxProcesses' => 2,
                'tries' => 2,
                'timeout' => 240,
            ],
            'invoice-supervisor' => [
                'connection' => 'redis',
                'queue' => ['invoices', 'receptions'],
                'balance' => 'auto',
                'processes' => 2,
                'minProcesses' => 2,
                'maxProcesses' => 2,
                'tries' => 1,
                'timeout' => 120,
            ],
            'invoice-resend-supervisor' => [
                'connection' => 'redis',
                'queue' => ['invoicing'],
                'balance' => 'simple',
                'processes' => 3,
                'minProcesses' => 3,
                'maxProcesses' => 3,
                'tries' => 1,
                'timeout' => 120,
            ],
            'sendbulk-supervisor' => [
                'connection' => 'redis',
                'queue' => ['sendbulk', 'createinvoice'],
                'balance' => 'auto',
                'processes' => 1,
                'minProcesses' => 1,
                'maxProcesses' => 1,
                'tries' => 1,
                'timeout' => 120,
            ],
            'smbulk-supervisor' => [
                'connection' => 'redis',
                'queue' => ['smbulk'],
                'balance' => 'auto',
                'processes' => 2,
                'minProcesses' => 2,
                'maxProcesses' => 2,
                'tries' => 1,
                'timeout' => 120,
            ],
            'imports-supervisor' => [
                'connection' => 'redis',
                'queue' => ['imports', 'gosocket'],
                'balance' => 'auto',
                'processes' => 2,
                'minProcesses' => 2,
                'maxProcesses' => 2,
                'tries' => 2,
                'timeout' => 120,
            ],
            'log-supervisor' => [
                'connection' => 'redis',
                'queue' => ['log_queue', 'query_pending'],
                'balance' => 'auto',
                'processes' => 2,
                'minProcesses' => 2,
                'maxProcesses' => 2,
                'tries' => 1,
                'timeout' => 120,
            ],
            'bulk-supervisor' => [
                'connection' => 'redis',
                'queue' => ['bulk'],
                'balance' => 'auto',
                'processes' => 1, //Solo 1 proceso a la vez para los cobros recurrentes.
                'minProcesses' => 1, //Solo 1 proceso a la vez para los cobros recurrentes.
                'maxProcesses' => 1, //Solo 1 proceso a la vez para los cobros recurrentes.
                'tries' => 1,
                'timeout' => 120,
            ],
            'subscriptions-supervisor' => [
                'connection' => 'redis',
                'queue' => ['payments'],
                'balance' => 'simple',
                'processes' => 1, //Solo 1 proceso a la vez para los cobros recurrentes.
                'minProcesses' => 1,
                'maxProcesses' => 1,
                'tries' => 1,
                'timeout' => 120,
            ],
        ],

        'local' => [
            'default-supervisor' => [
                'connection' => 'redis',
                'queue' => ['default'],
                'balance' => 'auto',
                'processes' => 3,
                'minProcesses' => 3,
                'maxProcesses' => 3,
                'tries' => 2,
                'timeout' => 120,
            ],
            'invoice-supervisor' => [
                'connection' => 'redis',
                'queue' => ['invoices', 'receptions'],
                'balance' => 'auto',
                'processes' => 2,
                'minProcesses' => 2,
                'maxProcesses' => 2,
                'tries' => 1,
                'timeout' => 120,
            ],
            'invoice-resend-supervisor' => [
                'connection' => 'redis',
                'queue' => ['invoicing'],
                'balance' => 'simple',
                'processes' => 3,
                'minProcesses' => 3,
                'maxProcesses' => 3,
                'tries' => 2,
                'timeout' => 120,
            ],
            'sendbulk-supervisor' => [
                'connection' => 'redis',
                'queue' => ['sendbulk', 'createinvoice'],
                'balance' => 'auto',
                'processes' => 2,
                'minProcesses' => 2,
                'maxProcesses' => 2,
                'tries' => 1,
                'timeout' => 120,
            ],
            'smbulk-supervisor' => [
                'connection' => 'redis',
                'queue' => ['smbulk'],
                'balance' => 'auto',
                'processes' => 2,
                'minProcesses' => 2,
                'maxProcesses' => 2,
                'tries' => 1,
                'timeout' => 120,
            ],
            'imports-supervisor' => [
                'connection' => 'redis',
                'queue' => ['imports', 'gosocket'],
                'balance' => 'auto',
                'processes' => 3,
                'minProcesses' => 3,
                'maxProcesses' => 3,
                'tries' => 2,
                'timeout' => 120,
            ],
            'log-supervisor' => [
                'connection' => 'redis',
                'queue' => ['log_queue', 'query_pending'],
                'balance' => 'auto',
                'processes' => 2,
                'minProcesses' => 2,
                'maxProcesses' => 2,
                'tries' => 1,
                'timeout' => 120,
            ],
            'bulk-supervisor' => [
                'connection' => 'redis',
                'queue' => ['bulk'],
                'balance' => 'auto',
                'processes' => 1, //Solo 1 proceso a la vez para los cobros recurrentes.
                'minProcesses' => 1, //Solo 1 proceso a la vez para los cobros recurrentes.
                'maxProcesses' => 1, //Solo 1 proceso a la vez para los cobros recurrentes.
                'tries' => 1,
                'timeout' => 120,
            ],
            'subscriptions-supervisor' => [
                'connection' => 'redis',
                'queue' => ['payments'],
                'balance' => 'simple',
                'processes' => 1, //Solo 1 proceso a la vez para los cobros recurrentes.
                'minProcesses' => 1,
                'maxProcesses' => 1,
                'tries' => 1,
                'timeout' => 120,
            ],
        ],
    ],
];
