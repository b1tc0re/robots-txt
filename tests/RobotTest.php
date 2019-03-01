<?php namespace DeftCMS\Components\b1tc0re\Robots;

use PHPUnit\Framework\TestCase;

class RobotTest extends TestCase
{
    public function testSitemap()
    {
        $robots  = new Robot();
        $sitemap = "sitemap.xml";
        $this->assertStringNotContainsString($sitemap, $robots->render());
        $robots->sitemap($sitemap);
        $this->assertStringContainsString("Sitemap: $sitemap", $robots->render());
    }

    public function testHost()
    {
        $robots = new Robot();
        $host   = "www.steein.ru";
        $this->assertStringNotContainsString("Host: $host", $robots->render());
        $robots->host($host);
        $this->assertStringContainsString("Host: $host", $robots->render());
    }

    public function testDisallow()
    {
        $robots = new Robot();
        $path = "dir";
        $this->assertStringNotContainsString($path,$robots->render());
        $robots->disallow($path);
        $this->assertStringContainsString($path, $robots->render());
    }

    public function testComment()
    {
        $robots    = new Robot();
        $comment_1 = "Steein comment";
        $comment_2 = "Test comment";
        $comment_3 = "Final test comment";
        $this->assertStringNotContainsString("# $comment_1", $robots->render());
        $this->assertStringNotContainsString("# $comment_2", $robots->render());
        $this->assertStringNotContainsString("# $comment_3", $robots->render());
        $robots->comment($comment_1);
        $this->assertStringContainsString("# $comment_1", $robots->render());
        $robots->comment($comment_2);
        $this->assertStringContainsString("# $comment_1", $robots->render());
        $this->assertStringContainsString("# $comment_2", $robots->render());
        $robots->comment($comment_3);
        $this->assertStringContainsString("# $comment_1", $robots->render());
        $this->assertStringContainsString("# $comment_2", $robots->render());
        $this->assertStringContainsString("# $comment_3", $robots->render());
    }

    public function testSpacer()
    {
        $robots = new Robot();
        $lines  = count(preg_split('/'. PHP_EOL .'/', $robots->render()));
        $this->assertEquals(1, $lines); // Starts off with at least one line.
        $robots->spacer();
        $lines = count(preg_split('/'. PHP_EOL .'/', $robots->render()));
        $this->assertEquals(2, $lines);
    }
}