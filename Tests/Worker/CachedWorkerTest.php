<?php

namespace jvdh\AsseticCachedWorker\Tests\Asset;
namespace jvdh\AsseticCachedWorker\Tests\Worker;

use Assetic\Asset\AssetCache;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\StringAsset;
use Assetic\Cache\ArrayCache;
use Assetic\Factory\AssetFactory;
use Assetic\Factory\Worker\WorkerInterface;
use jvdh\AsseticCachedWorker\Asset\AssetCacheCollection;
use jvdh\AsseticCachedWorker\Worker\CachedWorker;
use PHPUnit_Framework_Constraint_IsEqual as IsEqual;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

class CachedWorkerTest extends PHPUnit_Framework_TestCase
{
    public function testProcess_withStringAsset()
    {
        $asset = new StringAsset('#1');
        $cache = new ArrayCache();
        $assetCache = new AssetCache($asset, $cache);

        $mockedWorker = $this->getMockedWorker();
        $mockedWorker
            ->expects($this->once())
            ->method('process')
            ->with(new IsEqual($assetCache))
            ->willReturn($assetCache);
        $cachedWorker = new CachedWorker($mockedWorker, $cache);

        $assetFactory = $this->getMockedAssetFactory();
        $this->assertSame($assetCache, $cachedWorker->process($asset, $assetFactory));
    }

    public function testProcess_withAssetCollection()
    {
        $assetCollection = new AssetCollection(array(new StringAsset('#1'), new StringAsset('#2')));
        $cache = new ArrayCache();
        $assetCache = new AssetCacheCollection($assetCollection, $cache);

        $mockedWorker = $this->getMockedWorker();
        $mockedWorker
            ->expects($this->once())
            ->method('process')
            ->with(new IsEqual($assetCache))
            ->willReturn($assetCache);
        $cachedWorker = new CachedWorker($mockedWorker, $cache);

        $assetFactory = $this->getMockedAssetFactory();
        $this->assertSame($assetCache, $cachedWorker->process($assetCollection, $assetFactory));
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|WorkerInterface
     */
    private function getMockedWorker()
    {
        return $this->getMockBuilder('Assetic\Factory\Worker\WorkerInterface')->disableOriginalConstructor()->getMock();
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|AssetFactory
     */
    private function getMockedAssetFactory()
    {
        return $this->getMockBuilder('Assetic\Factory\AssetFactory')->disableOriginalConstructor()->getMock();
    }
}
