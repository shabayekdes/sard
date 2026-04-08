<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Central Storage Disk (non-tenant)
    |--------------------------------------------------------------------------
    |
    | Disk used for central-only uploads (e.g. bank payment attachments). Follows
    | the app default when not set: public -> central_public, s3 -> central_s3.
    | Set CENTRAL_FILESYSTEM_DISK=central_public|central_s3|central_gcs to override.
    |
    */
    'central_disk' => env('CENTRAL_FILESYSTEM_DISK') ?: match (env('FILESYSTEM_DISK', 'public')) {
        's3' => 'central_s3',
        'gcs' => 'central_gcs',
        default => 'central_public',
    },

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3", "gcs" (via spatie/laravel-google-cloud-storage)
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        /**
         * Central (non-tenant) local storage. Not in tenancy filesystem disks.
         */
        'central_public' => [
            'driver' => 'local',
            'root' => base_path('storage/app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        /**
         * Same bucket/credentials as default s3, with root prefix "central" so keys
         * are central/... instead of tenant-prefixed.
         */
        'central_s3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'root' => 'central',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        /**
         * Same bucket/credentials as default gcs, with root prefix "central" (Stancl tenancy uses
         * filesystems.disks.gcs.root for tenant-scoped paths; central uploads use this disk).
         */
        'central_gcs' => [
            'driver' => 'gcs',
            'key_file_path' => env('GOOGLE_CLOUD_KEY_FILE', null),
            'key_file' => [],
            'project_id' => env('GOOGLE_CLOUD_PROJECT_ID'),
            'bucket' => env('GOOGLE_CLOUD_STORAGE_BUCKET'),
            'root' => 'central',
            'storage_api_uri' => env('GOOGLE_CLOUD_STORAGE_API_URI', null),
            'api_endpoint' => env('GOOGLE_CLOUD_STORAGE_API_ENDPOINT', null),
            'visibility' => 'public',
            'visibility_handler' => \League\Flysystem\GoogleCloudStorage\UniformBucketLevelAccessVisibility::class,
            'metadata' => ['cacheControl' => 'public,max-age=86400'],
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'visibility' => 'public',
            'throw' => true,
            'report' => false,
        ],

        'gcs' => [
            'driver' => 'gcs',
            'key_file_path' => env('GOOGLE_APPLICATION_CREDENTIALS'), // optional: /path/to/service-account.json
            'key_file' => [], // optional: Array of data that substitutes the .json file (see below)
            'project_id' => env('GOOGLE_CLOUD_PROJECT_ID', ''), // optional: is included in key file
            'bucket' => env('GOOGLE_CLOUD_STORAGE_BUCKET', 'xxxxx'), //The bucket name
            'path_prefix' => env('GOOGLE_CLOUD_STORAGE_PATH_PREFIX', ''), // optional: /default/path/to/apply/in/bucket
            'storage_api_uri' => env('GOOGLE_CLOUD_STORAGE_API_URI', null), // see: Public URLs below
            'api_endpoint' => env('GOOGLE_CLOUD_STORAGE_API_ENDPOINT', null), // set storageClient apiEndpoint
            'visibility' => 'public', // optional: public|private
            'visibility_handler' => \League\Flysystem\GoogleCloudStorage\UniformBucketLevelAccessVisibility::class,
            'metadata' => ['cacheControl' => 'public,max-age=86400'], // optional: default metadata
            'throw' => true,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
