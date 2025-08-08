<?php

return [

    /*
     * Диск, на котором по умолчанию хранятся добавленные файлы и производные изображения. Выбирается
     * один или несколько дисков, которые были настроены в config/filesystems.php.
     */
    'disk_name' => env('MEDIA_DISK', 'public'),

    /*
     * Максимальный размер файла элемента в байтах.
     * Добавление файла большего размера приведет к исключению.
     */
    'max_file_size' => 1024 * 1024 * 10, // 10MB

    /*
     * Это соединение очереди будет использоваться для генерации производных и отзывчивых изображений.
     * Если оставить пустым, то будет использоваться соединение очереди по умолчанию.
     */
    'queue_connection_name' => '',

    /*
     * Эта очередь будет использоваться для создания производных и отзывчивых изображений.
     * Если оставить пустой, то будет использоваться очередь по умолчанию.
     */
    'queue_name' => '',

    /*
     * По умолчанию все преобразования будут выполняться в очереди.
     */
    'queue_conversions_by_default' => env('QUEUE_CONVERSIONS_BY_DEFAULT', true),

    /*
     * Полное имя класса медиамодели.
     */
    'media_model' => App\Models\Media::class,

    /*
     * Полное имя класса модели, используемой для временных загрузок.
     *
     * Эта модель используется только в Media Library Pro (https://medialibrary.pro)
     */
    'temporary_upload_model' => Spatie\MediaLibraryPro\Models\TemporaryUpload::class,

    /*
     * Если эта функция включена, Media Library Pro будет обрабатывать только временные загрузки, которые были загружены
     * в той же сессии. Можно отключить эту функцию для использования компонентов pro без статических данных
     */
    'enable_temporary_uploads_session_affinity' => true,

    /*
     * Если включено, Media Library pro будет генерировать миниатюры для загруженного файла.
     */
    'generate_thumbnails_for_temporary_uploads' => true,

    /*
     * Это класс, который отвечает за именование сгенерированных файлов.
     */
    'file_namer' => Spatie\MediaLibrary\Support\FileNamer\DefaultFileNamer::class,

    /*
     * Класс, содержащий стратегию определения пути к медиафайлу.
     */
    'path_generator' => Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator::class,

    /*
     * Когда генерируются урлы к файлам, вызывается этот класс. Используется по умолчанию.
     * если ваши файлы хранятся локально в корне сайта или на s3.
     */
    'url_generator' => Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator::class,

    /*
     * Активировать или нет версионность при генерации урлов к файлам.
     * При активации к URL прикрепляется строка запроса ?v=xx.
     */
    'version_urls' => false,

    /*
     * Медиатека попытается оптимизировать все преобразованные изображения, удалив
     * метаданных и применяя небольшое сжатие. Это
     * оптимизаторы, которые будут использоваться по умолчанию.
     */
    'image_optimizers' => [
        Spatie\ImageOptimizer\Optimizers\Jpegoptim::class => [
            '-m85', // set maximum quality to 85%
            '--strip-all', // this strips out all text information such as comments and EXIF data
            '--all-progressive', // this will make sure the resulting image is a progressive one
        ],
        Spatie\ImageOptimizer\Optimizers\Pngquant::class => [
            '--force', // required parameter for this package
        ],
        Spatie\ImageOptimizer\Optimizers\Optipng::class => [
            '-i0', // this will result in a non-interlaced, progressive scanned image
            '-o2', // this set the optimization level to two (multiple IDAT compression trials)
            '-quiet', // required parameter for this package
        ],
        Spatie\ImageOptimizer\Optimizers\Svgo::class => [
            '--disable=cleanupIDs', // disabling because it is known to cause troubles
        ],
        Spatie\ImageOptimizer\Optimizers\Gifsicle::class => [
            '-b', // required parameter for this package
            '-O3', // this produces the slowest but best results
        ],
        Spatie\ImageOptimizer\Optimizers\Cwebp::class => [
            '-m 6', // for the slowest compression method in order to get the best compression.
            '-pass 10', // for maximizing the amount of analysis pass.
            '-mt', // multithreading for some speed improvements.
            '-q 90', //quality factor that brings the least noticeable changes.
        ],
        Spatie\ImageOptimizer\Optimizers\Avifenc::class => [
            '-a cq-level=23', // constant quality level, lower values mean better quality and greater file size (0-63).
            '-j all', // number of jobs (worker threads, "all" uses all available cores).
            '--min 0', // min quantizer for color (0-63).
            '--max 63', // max quantizer for color (0-63).
            '--minalpha 0', // min quantizer for alpha (0-63).
            '--maxalpha 63', // max quantizer for alpha (0-63).
            '-a end-usage=q', // rate control mode set to Constant Quality mode.
            '-a tune=ssim', // SSIM as tune the encoder for distortion metric.
        ],
    ],

    /*
     * Эти генераторы будут использоваться для создания изображения медиафайлов.
     */
    'image_generators' => [
        Spatie\MediaLibrary\Conversions\ImageGenerators\Image::class,
        Spatie\MediaLibrary\Conversions\ImageGenerators\Webp::class,
        Spatie\MediaLibrary\Conversions\ImageGenerators\Avif::class,
        Spatie\MediaLibrary\Conversions\ImageGenerators\Pdf::class,
        Spatie\MediaLibrary\Conversions\ImageGenerators\Svg::class,
        Spatie\MediaLibrary\Conversions\ImageGenerators\Video::class,
    ],

    /*
     * Путь, где хранить временные файлы при преобразовании изображений.
     * Если установлено значение null, будет использоваться storage_path('media-library/temp').
     */
    'temporary_directory_path' => null,

    /*
     * Движок, который должен выполнять преобразование изображений.
     * Должен быть либо `gd`, либо `imagick`.
     */
    'image_driver' => env('IMAGE_DRIVER', 'gd'),

    /*
     * Пути к двоичным файлам FFMPEG и FFProbe, используются только в том случае, если вы пытаетесь сгенерировать видео.
     * миниатюры и установлена зависимость php-ffmpeg/php-ffmpeg composer
     * зависимость.
     */
    'ffmpeg_path' => env('FFMPEG_PATH', '/usr/bin/ffmpeg'),
    'ffprobe_path' => env('FFPROBE_PATH', '/usr/bin/ffprobe'),

    /*
     * Здесь можно переопределить имена классов заданий, используемых этим пакетом. Нужно убедиться, что
     * пользовательские задания расширяют предоставленные пакетом.
     */
    'jobs' => [
        'perform_conversions' => Spatie\MediaLibrary\Conversions\Jobs\PerformConversionsJob::class,
        'generate_responsive_images' => Spatie\MediaLibrary\ResponsiveImages\Jobs\GenerateResponsiveImagesJob::class,
    ],

    /*
     * При использовании метода addMediaFromUrl можно заменить загрузчик по умолчанию.
     * Это особенно полезно, когда url изображения находится за брандмауэром и
     * необходимо добавить дополнительные флаги, возможно, используя curl.
     */
    'media_downloader' => Spatie\MediaLibrary\Downloaders\DefaultDownloader::class,

    'remote' => [
        /*
         * Любые дополнительные заголовки, которые должны быть включены при загрузке мультимедиа на
         * удаленный диск. Несмотря на то, что поддерживаемые заголовки могут различаться между
         * различными драйверами, было установлено оптимальное значение по умолчанию.
         *
         * Поддерживается S3: CacheControl, Expires, StorageClass,
         * ServerSideEncryption, Metadata, ACL, ContentEncoding (кодировка содержимого)
         */
        'extra_headers' => [
            'CacheControl' => 'max-age=604800',
        ],
    ],

    'responsive_images' => [
        /*
         * Этот класс отвечает за вычисление целевой ширины изображений. 
         * По умолчанию оптимизируются по размеру файлов и создаются варианты, каждый из которых на 20%
         * меньше предыдущей. Подробнее в документации.
         *
         * https://spatie.be/docs/laravel-medialibrary/v11/responsive-images/getting-started-with-responsive-images
         */
        'width_calculator' => Spatie\MediaLibrary\ResponsiveImages\WidthCalculator\FileSizeOptimizedWidthCalculator::class,

        /*
         * По умолчанию при рендеринге медиа в отзывчивое изображение добавляется немного javascript и крошечный placeholder.
         * Это гарантирует, что браузер сможет определить правильное расположение.
         */
        'use_tiny_placeholders' => true,

        /*
         * Этот класс будет генерировать крошечный плагин, используемый для прогрессивной загрузки изображений. По умолчанию
         * медиатека будет использовать крошечное размытое изображение jpg.
         */
        'tiny_placeholder_generator' => Spatie\MediaLibrary\ResponsiveImages\TinyPlaceholderGenerator\Blurred::class,
    ],

    /*
     * При включении этой опции будет зарегистрирован маршрут, который позволит
     * компонентам Media Library Pro Vue и React перемещать загруженные файлы
     * в ведро S3 в нужное место.
     */
    'enable_vapor_uploads' => env('ENABLE_MEDIA_LIBRARY_VAPOR_UPLOADS', false),

    /*
     * При преобразовании экземпляров Media в ответ медиатека добавляет.
     * атрибут `loading` к тегу `img`. Ниже можно установить значение по умолчанию
     * этого атрибута по умолчанию.
     *
     * Возможные значения: 'lazy', 'eager', 'auto' или null, если не нужно задавать никаких инструкций по загрузке.
     *
     * Дополнительная информация: https://css-tricks.com/native-lazy-loading/
     */
    'default_loading_attribute_value' => null,

    /*
     * Можно указать префикс, который будет использоваться для хранения всех медиафайлов.
     * Если задать `/my-subdir`, все медиа будут храниться в директории `/my-subdir`.
     */
    'prefix' => env('MEDIA_PREFIX', ''),
];
