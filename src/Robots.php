<?php namespace DeftCMS\Components\b1tc0re\Robots;

use Closure;

class Robots implements IRobot
{
    /**
     * Robots parser
     *
     * @var RobotsParserWrap
     */
    private RobotsParserWrap $storage;

    /**
     * Статический экземпляр класса
     *
     * @var null|Robots
     */
    private static ?Robots $_instance;

    /**
     * Метод возврата статического экземпляра
     *
     * @return $this
     * @uses $robots = Robots::getInstance();
     *
     */
    public static function getInstance(): self
    {
        self::$_instance || (self::$_instance = new static());
        return self::$_instance;
    }

    /**
     * Robot constructor.
     *
     * @param string $path
     */
    public function __construct(string $path = "robots.txt")
    {
        $this->storage = $this->getRobotsStorage($path);
    }

    /**
     * Добавить хост в файл robots.txt.
     *
     * @param string $host
     * @return IRobot
     */
    public function host(string $host): IRobot
    {
        $this->storage->setHost($host);
        return $this;
    }

    /**
     * Добавить ссылку на файл SiteMap в файл robots.txt.
     *
     * @param string|array $siteMap
     * @return IRobot
     *
     */
    public function siteMap(...$siteMap): IRobot
    {
        $this->storage->addSitemap($siteMap);
        return $this;
    }

    /**
     * Удалить ссылку на файл SiteMap в файл robots.txt.
     *
     * @param string|array $siteMap
     * @return IRobot
     */
    public function removeSiteMap(...$siteMap): IRobot
    {
        $this->storage->removeSiteMap($siteMap);
        return $this;
    }

    /**
     * Удалить все ссылки на файл SiteMap в файл robots.txt.
     *
     * @return IRobot
     */
    public function cleanSiteMap(): IRobot
    {
        $this->storage->cleanSiteMap();
        return $this;
    }

    /**
     * Добавить GET-параметры (например, идентификаторы сессий, пользователей) или метки (напрмиер, UTM), которые не влияют на их содержимое.
     *
     * @param array $params
     * @return IRobot
     */
    public function cleanParams(array $params): IRobot
    {
        $this->storage->addCleanParams($params);
        return $this;
    }

    /**
     * Задать поисковым роботу минимальный период времени (в секундах)
     *
     * @param float|int $seconds
     * @param string $type
     * @return IRobot
     */
    public function crawlDelay(float|int $seconds, string $type = 'crawl-delay'): IRobot
    {
        switch (mb_strtolower($type)) {
            case 'cache':
            case 'cache-delay':
                // non-standard directive
                $directive = RobotsParserWrap::DIRECTIVE_CACHE_DELAY;
                break;
            default:
                $directive = RobotsParserWrap::DIRECTIVE_CRAWL_DELAY;
        }

        $this->storage->addRule($seconds, $directive);
        return $this;
    }

    /**
     * Задать поисковому роботу минимальный период времени (в секундах)
     * @param string $agent
     * @param int|float $seconds
     * @param string $type
     * @return IRobot
     */
    public function crawlDelayForAgent(string $agent, int|float $seconds, string $type = 'crawl-delay'): IRobot
    {
        $directive = match (mb_strtolower($type)) {
            'cache', 'cache-delay' => RobotsParserWrap::DIRECTIVE_CACHE_DELAY,
            default => RobotsParserWrap::DIRECTIVE_CRAWL_DELAY,
        };

        $this->storage->addRuleForAgent($agent, $seconds, $directive);
        return $this;
    }

    /**
     * Добавить правило запрета в файл robots.txt.
     * @param string|array ...$directories
     * @return IRobot
     */
    public function disallow(...$directories): IRobot
    {
        foreach ((array)$directories as $directory) {
            $this->storage->addRule($this->directoryNormalize($directory), 'disallow');
        }

        return $this;
    }

    /**
     * Добавить правило запрета для роботов
     * @param string $agent
     * @param string $directory
     * @return IRobot
     */
    public function disallowForAgent(string $agent, string $directory): IRobot
    {
        $this->storage->addRuleForAgent($agent, $this->directoryNormalize($directory), 'Disallow');
        return $this;
    }

    /**
     * Удалить правило запрета для роботов
     * @param string|array ...$directories
     */
    public function disallowRemove(...$directories)
    {
        foreach ((array)$directories as $directory) {
            $this->storage->removeRule($this->directoryNormalize($directory), 'disallow');
        }
    }

    /**
     * Добавить правило разрешения в файл robots.txt.
     *
     * @param string|array $directories
     * @return IRobot
     */
    public function allow(...$directories): IRobot
    {
        foreach ((array)$directories as $directory) {
            $this->storage->addRule($this->directoryNormalize($directory), 'Allow');
        }

        return $this;
    }

    /**
     * Добавить правило разрешения в файл robots.txt. для $agent
     * @param string $agent
     * @param string $directory
     * @return IRobot
     */
    public function allowForAgent(string $agent, string $directory): IRobot
    {
        $this->storage->addRuleForAgent($agent, $this->directoryNormalize($directory), 'Allow');

        return $this;
    }

    /**
     * Generate the robots.txt and return content
     * @return string
     */
    public function render(): string
    {
        return $this->storage->render();
    }

    /**
     * Обновить данные robots.txt
     *
     * @param string $path
     *
     * @return void
     */
    public function update(string $path = 'robots.txt'): void
    {
        if (file_exists($path)) {
            unlink($path);
        }

        file_put_contents($path, $this->render());
    }

    /**
     * Trailing slash
     *
     * @param string $directory
     *
     * @return string
     */
    private function directoryNormalize(string $directory): string
    {
        $directory = '/' . trim($directory, '/') . '/';
        return preg_replace('#(^|[^:])//+#', '\\1/', $directory);
    }

    /**
     * Get instance
     * @param string $content
     * @return RobotsParserWrap
     */
    private function getRobotsStorage(string $content): RobotsParserWrap
    {
        if (is_file($content) || filter_var($content, FILTER_VALIDATE_URL)) {
            $content = file_get_contents($content);
        }

        return new RobotsParserWrap($content);
    }
}