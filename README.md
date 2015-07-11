# YoutubeHelper

A tiny package to convenience work with YouTube media resources.
Allow to extract ID from resource URL and build valid resource URLs.

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/73b4b27a-156d-4ea7-925a-807bc18e5898/mini.png)](https://insight.sensiolabs.com/projects/73b4b27a-156d-4ea7-925a-807bc18e5898)
[![Build Status](https://travis-ci.org/LapaLabs/YoutubeHelper.svg?branch=master)](https://travis-ci.org/LapaLabs/YoutubeHelper)

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

You can easily get HTML code to embed YouTube resource on your page:

``` php
$resource->buildEmbedCode(); // with default attributes returns: <iframe width="560" height="315" src="https://www.youtube.com/embed/5qanlirrRWs" frameborder="0" allowfullscreen></iframe>

// to pass any other parameters or override defaults with your own use:
$resource->buildEmbedCode([
    'width' => 800,     // override default 560
    'height' => 600,    // override default 315
    'class' => 'video', // add new attribute 
]);
```

>   The passed attributes to `buildEmbedCode()` methods applies for current embed HTML code only.
    To change default attributes globally for specific resource you should pass an array of attributes
    to `setAttributes()` method. To get current default HTML attributes of specific resource use
    `getAttributes()` method.

There are a few attributes by default:

``` php
[
    'width'           => 560,
    'height'          => 315,
    'src'             => '', // hold position for specific order
    'frameborder'     => 0,
    'allowfullscreen' => null,
];
```

## Links

Feel free to create an [Issue][1] or [Pull Request][2] if you find a bug 
or just want to propose improvement suggestion.

[Move UP](#youtubehelper)


[1]: https://github.com/LapaLabs/YoutubeHelper/issues
[2]: https://github.com//LapaLabs/YoutubeHelper/pulls
