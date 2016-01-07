<?php

namespace jvdh\AsseticCachedWorker\Tests\Asset;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\AssetCollectionInterface;
use Assetic\Asset\StringAsset;
use Assetic\Cache\ArrayCache;
use InvalidArgumentException;
use IteratorAggregate;
use jvdh\AsseticCachedWorker\Asset\AssetCacheCollection;
use PHPUnit_Framework_Constraint_IsIdentical as IsIdentical;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

class AssetCacheCollectionTest extends PHPUnit_Framework_TestCase
{
    public function testAdd()
    {
        $asset = new StringAsset('');

        $mockedCollection = $this->getMockedAssetCollection();
        $mockedCollection->expects($this->once())->method('add')->with(new IsIdentical($asset));

        $cachedCollection = new AssetCacheCollection($mockedCollection, new ArrayCache());
        $cachedCollection->add($asset);
    }
    public function testAll()
    {
        $assets = array(new StringAsset('#1'), new StringAsset('#2'));

        $mockedCollection = $this->getMockedAssetCollection();
        $mockedCollection->expects($this->once())->method('all')->with()->willReturn($assets);

        $cachedCollection = new AssetCacheCollection($mockedCollection, new ArrayCache());
        $this->assertSame($assets, $cachedCollection->all());
    }

    public function testRemoveLeaf_graceful_returnsFalse()
    {
        $asset = new StringAsset('#1');

        $mockedCollection = $this->getMockedAssetCollection();
        $mockedCollection
            ->expects($this->once())
            ->method('removeLeaf')
            ->with(new IsIdentical($asset), true)
            ->willReturn(false);

        $cachedCollection = new AssetCacheCollection($mockedCollection, new ArrayCache());
        $this->assertFalse($cachedCollection->removeLeaf($asset, true));
    }

    public function testRemoveLeaf_notGraceful_throwsException()
    {
        $asset = new StringAsset('#1');
        $exception = new InvalidArgumentException('Leaf not found.');

        $mockedCollection = $this->getMockedAssetCollection();
        $mockedCollection
            ->expects($this->once())
            ->method('removeLeaf')
            ->with(new IsIdentical($asset), false)
            ->willThrowException($exception);

        $this->setExpectedException(get_class($exception), $exception->getMessage());

        $cachedCollection = new AssetCacheCollection($mockedCollection, new ArrayCache());
        $cachedCollection->removeLeaf($asset, false);
    }

    public function testReplaceLeaf_graceful_returnsFalse()
    {
        $asset1 = new StringAsset('#1');
        $asset2 = new StringAsset('#2');

        $mockedCollection = $this->getMockedAssetCollection();
        $mockedCollection
            ->expects($this->once())
            ->method('replaceLeaf')
            ->with(new IsIdentical($asset1), new IsIdentical($asset2), true)
            ->willReturn(false);

        $cachedCollection = new AssetCacheCollection($mockedCollection, new ArrayCache());
        $this->assertFalse($cachedCollection->replaceLeaf($asset1, $asset2, true));
    }

    public function testReplaceLeaf_notGraceful_throwsException()
    {
        $asset1 = new StringAsset('#1');
        $asset2 = new StringAsset('#2');
        $exception = new InvalidArgumentException('Leaf not found.');

        $mockedCollection = $this->getMockedAssetCollection();
        $mockedCollection
            ->expects($this->once())
            ->method('replaceLeaf')
            ->with(new IsIdentical($asset1), new IsIdentical($asset2), false)
            ->willThrowException($exception);

        $this->setExpectedException(get_class($exception), $exception->getMessage());

        $cachedCollection = new AssetCacheCollection($mockedCollection, new ArrayCache());
        $cachedCollection->replaceLeaf($asset1, $asset2, false);
    }

    public function testGetIterator()
    {
        $mockedCollection = $this->getMockedAssetCollection();
        $cachedCollection = new AssetCacheCollection($mockedCollection, new ArrayCache());

        $this->assertSame($mockedCollection, $cachedCollection->getIterator());
        $this->assertInstanceOf(IteratorAggregate::class, $cachedCollection);
    }

    public function testLoad_withoutCache_loadsFromOriginal()
    {
        $cacheKey = md5('0load');
        $content = 'original';
        $asset = new StringAsset($content);
        $assetCollection = new AssetCollection(array($asset));

        $cache = new ArrayCache();
        $cachedCollection = new AssetCacheCollection($assetCollection, $cache);
        $cachedCollection->load();

        $this->assertSame($content, $cache->get($cacheKey));
    }

    public function testLoad_withCache_loadsFromCache()
    {
        $content = 'cache';
        $cacheKey = md5('load');

        $mockedAssetCollection = $this->getMockedAssetCollection();
        $mockedAssetCollection
            ->expects($this->once())
            ->method('getFilters')
            ->willReturn(array());

        $mockedAssetCollection
            ->expects($this->never())
            ->method('load');

        $cache = new ArrayCache();
        $cache->set($cacheKey, $content);

        $cachedCollection = new AssetCacheCollection($mockedAssetCollection, $cache);
        $cachedCollection->load();
    }

    public function testDump_withoutCache_loadsFromOriginal()
    {
        $cacheKey = md5('0dump');
        $content = 'original';
        $asset = new StringAsset($content);
        $assetCollection = new AssetCollection(array($asset));

        $cache = new ArrayCache();
        $cachedCollection = new AssetCacheCollection($assetCollection, $cache);

        $this->assertSame($content, $cachedCollection->dump());
        $this->assertSame($content, $cache->get($cacheKey));
    }

    public function testDump_withCache_loadsFromCache()
    {
        $cacheKey = md5('dump');
        $content = 'cache';

        $mockedAssetCollection = $this->getMockedAssetCollection();
        $mockedAssetCollection
            ->expects($this->once())
            ->method('getFilters')
            ->willReturn(array());

        $mockedAssetCollection
            ->expects($this->never())
            ->method('dump');

        $cache = new ArrayCache();
        $cache->set($cacheKey, $content);

        $cachedCollection = new AssetCacheCollection($mockedAssetCollection, $cache);
        $this->assertSame($content, $cachedCollection->dump());
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|AssetCollectionInterface
     */
    private function getMockedAssetCollection()
    {
        return $this->getMockBuilder(AssetCollectionInterface::class)->disableOriginalConstructor()->getMock();
    }
}
