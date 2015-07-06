# YoutubeHelper

A tiny package to convenience work with YouTube media resources.
Allow to extract ID from resource URL and build valid resource URLs.   

## Installation

Install package to your project with `Composer`:

``` bash
$ composer require lapalabs/youtube-helper dev-master
```

## Usage

You can easily create valid YouTube resource:

``` php
use LapaLabs\YoutubeExtractor\Resource\YoutubeResource;

// Build resource object from valid YouTube resource ID
$resource = new YoutubeResource('5qanlirrRWs');
// or with static method
$resource = YoutubeResource::create('5qanlirrRWs');

// or create from valid YouTube resource URL
$resource = YoutubeResource::createFromUrl('https://www.youtube.com/watch?v=5qanlirrRWs');

$resource->getId();         // 5qanlirrRWs
$resource->buildUrl();      // https://www.youtube.com/watch?v=5qanlirrRWs
$resource->buildEmbedUrl(); // https://www.youtube.com/embed/5qanlirrRWs
```

There are a few valid YouTube resource URLs, supported by this library,
that should be used in `YoutubeResource::createFromUrl()` method:

* `https://youtube.com/watch?v=5qanlirrRWs`
* `https://m.youtube.com/watch?v=5qanlirrRWs`
* `https://www.youtube.com/watch?v=5qanlirrRWs`
* `https://www.youtube.com/embed/5qanlirrRWs`
* `https://youtu.be/5qanlirrRWs`
