<?php

namespace Kucera\Monolog\Tests;

use Kucera\Monolog\Factory;

class BlueScreenFactoryTest extends \Kucera\Monolog\Tests\TestCase
{

    public function testBlueScreen()
    {
        $blueScreen = Factory::blueScreen();
        $this->assertInstanceOf('Tracy\BlueScreen', $blueScreen);
    }

    public function testBlueScreenHandler()
    {
        $handler = Factory::blueScreenHandler(__DIR__);
        $this->assertInstanceOf('Kucera\Monolog\Handler\BlueScreenHandler', $handler);
    }

}
