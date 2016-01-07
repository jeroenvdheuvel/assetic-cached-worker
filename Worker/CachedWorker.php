<?php

namespace jvdh\AsseticCachedWorker\Worker;

use Assetic\Asset\AssetCache;
use Assetic\Asset\AssetCollectionInterface;
use Assetic\Asset\AssetInterface;
use Assetic\Cache\CacheInterface;
use Assetic\Factory\AssetFactory;
use Assetic\Factory\Worker\WorkerInterface;
use jvdh\AsseticCachedWorker\Asset\AssetCacheCollection;

class CachedWorker implements WorkerInterface
{
    /**
     * @var WorkerInterface
     */
    private $worker;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @param WorkerInterface $worker
     * @param CacheInterface $cache
     */
    public function __construct(WorkerInterface $worker, CacheInterface $cache)
    {
        $this->worker = $worker;
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function process(AssetInterface $asset, AssetFactory $factory)
    {
        if ($asset instanceof AssetCollectionInterface) {
            $cacheAsset = new AssetCacheCollection($asset, $this->cache);
        } else {
            $cacheAsset = new AssetCache($asset, $this->cache);
        }

        return $this->worker->process($cacheAsset, $factory);
    }
}
