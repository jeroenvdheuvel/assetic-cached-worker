
README
======

Master: [![Build Status](https://travis-ci.org/jeroenvdheuvel/assetic-cached-worker.svg)](https://travis-ci.org/jeroenvdheuvel/assetic-cached-worker)

Description
-----------
Assetic worker caches assets from other workers.
The worker is able to cache AssetCollectionInterface and AssetInterface objects.

Caching assets can be helpful when the assets are reused multiple times.
For instance by twig templates with dynamic assets like css files contain a hash filename that is a sum of the content.
When a single asset is used multiple times the filename is compiled multiple times to get the sum.
By using the cached worker the css file is cached, only the sum is calculated each time.
