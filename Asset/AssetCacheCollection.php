<?php

namespace jvdh\AsseticCachedWorker\Asset;

use Assetic\Asset\AssetCache;
use Assetic\Asset\AssetCollectionInterface;
use Assetic\Asset\AssetInterface;
use Assetic\Cache\CacheInterface;

class AssetCacheCollection extends AssetCache implements \IteratorAggregate, AssetCollectionInterface
{
    /**
     * @var AssetCollectionInterface
     */
    private $assetCollection;

    /**
     * @param AssetCollectionInterface $assetCollection
     * @param CacheInterface $cache
     */
    public function __construct(AssetCollectionInterface $assetCollection, CacheInterface $cache)
    {
        parent::__construct($assetCollection, $cache);

        $this->assetCollection = $assetCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->assetCollection->all();
    }

    /**
     * {@inheritdoc}
     */
    public function add(AssetInterface $asset)
    {
        $this->assetCollection->add($asset);
    }

    /**
     * {@inheritdoc}
     */
    public function removeLeaf(AssetInterface $leaf, $graceful = false)
    {
        return $this->assetCollection->removeLeaf($leaf, $graceful);
    }

    /**
     * {@inheritdoc}
     */
    public function replaceLeaf(AssetInterface $needle, AssetInterface $replacement, $graceful = false)
    {
        return $this->assetCollection->replaceLeaf($needle, $replacement, $graceful);
    }

    /**
     * @return AssetCollectionInterface
     */
    public function getIterator()
    {
        return $this->assetCollection;
    }
}
