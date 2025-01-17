<?php

use TightenCo\Jigsaw\Jigsaw;

/** @var \Illuminate\Container\Container $container */
/** @var \TightenCo\Jigsaw\Events\EventBus $events */
if (class_exists("Dotenv\Dotenv")) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();
}

/*
 * You can run custom code at different stages of the build process by
 * listening to the 'beforeBuild', 'afterCollections', and 'afterBuild' events.
 *
 * For example:
 *
 * $events->beforeBuild(function (Jigsaw $jigsaw) {
 *     // Your code here
 * });
 */

$events->beforeBuild(function (Jigsaw $jigsaw) {
    $manifest = json_decode(file_get_contents(__DIR__.'/source/assets/debugbar/manifest.json'), true);

    $jigsaw->setConfig('debugbarAssets', [
        'js' => basename($manifest['src/demo.ts']['file']),
    ]);
});

$events->afterCollections(function (Jigsaw $jigsaw) {
    global $docsToc; // YOLO

    $docsToc = $jigsaw->getCollection('docs')->map(function ($page) {
        return [
            'title' => $page->title,
            'section' => $page->toc_section,
            'url' => $page->getPath(),
            'disabled' => $page->disabled ?? false,
        ];
    })->values()->groupBy('section');
});

\Torchlight\Jigsaw\TorchlightExtension::make($container, $events)->boot();
