# YoutubeHelper

A tiny package to convenience work with YouTube media resources.
Allow to extract ID from resource URL and build valid resource URLs.

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/73b4b27a-156d-4ea7-925a-807bc18e5898/mini.png)](https://insight.sensiolabs.com/projects/73b4b27a-156d-4ea7-925a-807bc18e5898)

## Installation

Install package to your project with `Composer`:

``` bash
$ composer require lapalabs/youtube-helper dev-master
```

## Usage

### Creation

You can easily create valid YouTube resource object:

``` php
use LapaLabs\YoutubeExtractor\Resource\YoutubeResource;

// Build resource object from valid YouTube resource ID
$resource = new YoutubeResource('5qanlirrRWs');
// or with static method
$resource = YoutubeResource::create('5qanlirrRWs');

// or create from valid YouTube resource URL
$resource = YoutubeResource::createFromUrl('https://www.youtube.com/watch?v=5qanlirrRWs');
```

There are a few valid YouTube resource URLs, supported by this library,
that should be used in `YoutubeResource::createFromUrl()` method:

* `https://youtube.com/watch?v=5qanlirrRWs`
* `https://m.youtube.com/watch?v=5qanlirrRWs`
* `https://www.youtube.com/watch?v=5qanlirrRWs`
* `https://www.youtube.com/embed/5qanlirrRWs`
* `https://youtu.be/5qanlirrRWs`

### Advanced usage

After resource was successfully created you get access to a bunch of useful methods:

``` php
$resource->getId();           // 5qanlirrRWs
$resource->buildEmbedUrl();   // https://www.youtube.com/embed/5qanlirrRWs

// other useful methods to build various valid URLs
$resource->buildUrl();        // shortcut alias for buildDefaultUrl
$resource->buildDefaultUrl(); // https://www.youtube.com/watch?v=5qanlirrRWs
$resource->buildAliasUrl();   // https://youtube.com/watch?v=5qanlirrRWs
$resource->buildMobileUrl();  // https://m.youtube.com/watch?v=5qanlirrRWs
$resource->buildShortUrl();   // https://youtu.be/5qanlirrRWs
```

You can get array of valid YouTube resource URLs which could used in `createFromUrl` method:

``` php
YoutubeResource::getValidHosts(); // array of valid YouTube resource URLs
```

To check whether YouTube resource ID or host is valid use follow methods:
 
``` php
YoutubeResource::isValidId('5qanlirrRWs'); // return true if ID is valid
YoutubeResource::isValidHost('youtu.be');  // return true if host is valid
```
