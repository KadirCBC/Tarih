<?php

return [

    'backup' => [

        /*
         * The name of this application. You can use this name to monitor
         * the backups.
         */
        'name' => env('APP_NAME', 'Antik-Numismatik-Katalogu'),

        'source' => [

            'files' => [

                /*
                 * The list of files and directories that should be backed up.
                 * Includes catalog high-resolution coin images.
                 */
                'include' => [
                    storage_path('app/public/coins'),
                ],

                /*
                 * These paths will be excluded from the backup.
                 */
                'exclude' => [
                    storage_path('app/public/identifications'), // Geçici AI taramaları hariç tutulur
                    storage_path('app/private'),
                    base_path('vendor'),
                    base_path('node_modules'),
                ],

                /*
                 * Determines if symlinks should be followed.
                 */
                'follow_links' => false,

                /*
                 * Determines if it should avoid unreadable folders.
                 */
                'ignore_unreadable_directories' => true,

                /*
                 * This submodule will be used to generate the temporary file path.
                 */
                'relative_path' => null,
            ],

            /*
             * The names of the connections to the databases that should be backed up
             * MySQL dumped with gzip compression.
             */
            'databases' => [
                env('DB_CONNECTION', 'mysql'),
            ],
        ],

        /*
         * The database dump can be compressed to decrease disk space usage.
         */
        'database_dump_compressor' => Spatie\Backup\Tasks\Backup\Zip::class,

        /*
         * The file extension for the database dump file.
         */
        'database_dump_file_extension' => '',

        'destination' => [

            /*
             * The compression algorithm to be used for creating the zip archive.
             */
            'compression_method' => ZipArchive::CM_DEFLATE,

            'compression_level' => 9,

            /*
             * The filename prefix used for backup files.
             */
            'filename_prefix' => 'numismatic_backup_',

            /*
             * The disk names on which the backups will be stored.
             * Backups are pushed to remote S3/Wasabi/FTP as well as local fallback.
             */
            'disks' => [
                env('BACKUP_DESTINATION_DISK', 'backup_remote'),
            ],
        ],

        /*
         * Temporary directory where the backup task can store temporary files.
         */
        'temporary_directory' => storage_path('app/backup-temp'),

        'password' => env('BACKUP_ARCHIVE_PASSWORD', null),

        'encryption' => 'default',

        'tries' => 2,

        'retry_delay' => 60,
    ],

    /*
     * Clean up strategy for old backups to save storage on cPanel/VDS/Cloud.
     */
    'cleanup' => [
        'strategy' => Spatie\Backup\Tasks\Cleanup\Strategies\DefaultStrategy::class,

        'default_strategy' => [

            /*
             * The number of days for which backups must be kept.
             */
            'keep_all_backups_for_days' => 7,

            /*
             * The number of days for which daily backups must be kept.
             */
            'keep_daily_backups_for_days' => 16,

            /*
             * The number of weeks for which one weekly backup must be kept.
             */
            'keep_weekly_backups_for_weeks' => 8,

            /*
             * The number of months for which one monthly backup must be kept.
             */
            'keep_monthly_backups_for_months' => 4,

            /*
             * The number of years for which one yearly backup must be kept.
             */
            'keep_yearly_backups_for_years' => 1,

            /*
             * After cleaning up the backups, remove the oldest backup until this amount of megabytes has been reached.
             */
            'delete_oldest_backups_when_using_more_megabytes_than' => 10000, // 10 GB limit
        ],

        'tries' => 2,

        'retry_delay' => 60,
    ],

    'notifications' => [
        'notifications' => [
            Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification::class => ['mail'],
            Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFoundNotification::class => ['mail'],
            Spatie\Backup\Notifications\Notifications\CleanupHasFailedNotification::class => ['mail'],
            Spatie\Backup\Notifications\Notifications\BackupSuccessfulNotification::class => [],
            Spatie\Backup\Notifications\Notifications\HealthyBackupWasFoundNotification::class => [],
            Spatie\Backup\Notifications\Notifications\CleanupSuccessfulNotification::class => [],
        ],

        'notifiable' => Spatie\Backup\Notifications\Notifiable::class,

        'mail' => [
            'to' => env('BACKUP_NOTIFICATION_EMAIL', 'admin@antiknumismatik.com'),
            'from' => [
                'address' => env('MAIL_FROM_ADDRESS', 'noreply@antiknumismatik.com'),
                'name' => env('MAIL_FROM_NAME', 'Numismatik Backup Bot'),
            ],
        ],

        'slack' => [
            'webhook_url' => '',
            'channel' => null,
            'username' => null,
            'icon' => null,
        ],

        'discord' => [
            'webhook_url' => '',
            'username' => null,
            'avatar_url' => null,
        ],
    ],

    /*
     * Health checks will be performed on the backup storage.
     */
    'monitor_backups' => [
        [
            'name' => env('APP_NAME', 'Antik-Numismatik-Katalogu'),
            'disks' => [env('BACKUP_DESTINATION_DISK', 'backup_remote')],
            'health_checks' => [
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumAgeInDays::class => 1,
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumStorageInMegabytes::class => 15000,
            ],
        ],
    ],

];

