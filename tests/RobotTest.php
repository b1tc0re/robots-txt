<?php namespace DeftCMS\Components\b1tc0re\Robots;

use PHPUnit\Framework\TestCase;

class RobotTest extends TestCase
{
    public function testHost()
    {
        $robots  = Robots::getInstance();
        $value = 'https://www.example.ru';

        $this->assertStringNotContainsString($value, $robots->render());
        $robots->host($value);
        $this->assertStringContainsString($value, $robots->render());

    }

    public function testSiteMapHost()
    {
        $robots  = Robots::getInstance();
        $value = 'https://www.example.ru/site/map.xml';
        $this->assertStringNotContainsString($value, $robots->render());

        $robots->siteMap($value);
        $this->assertStringContainsString($value, $robots->render());
    }

    public function testCleanParams()
    {
        $robots  = Robots::getInstance();

        $value = '&param&param2';
        $this->assertStringNotContainsString($value, $robots->render());

        $robots->cleanParams(explode('&', $value));
        $this->assertStringContainsString($value, $robots->render());
    }

    public function testCrawlDelayHost()
    {
       $robots  = Robots::getInstance();

       $value = 2525;
       $this->assertStringNotContainsString($value, $robots->render());

       $robots->crawlDelay($value);
       $this->assertStringContainsString($value, $robots->render());
    }

    public function testDisallow()
    {
       $robots  = Robots::getInstance();
       $value = '/path/contact';
       $this->assertStringNotContainsString($value, $robots->render());

       $robots->disallow($value);
       $this->assertStringContainsString($value, $robots->render());
    }

    public function testAllow()
    {
       $robots  = Robots::getInstance();
       $value = '/path/contact';
       $this->assertStringNotContainsString($value, $robots->render());

       $robots->allow($value);
       $this->assertStringContainsString($value, $robots->render());
    }
}