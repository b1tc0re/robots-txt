<?php  namespace DeftCMS\Components\b1tc0re\Robots;

use Closure;

class Robots implements IRobot
{
    /**
     * Выполните обратный вызов для каждого пакетного агента.
     *
     * @param callable| Closure $closure
     */
    public function testEach(\Closure $closure)
    {
        $this->storage->each($closure);
    }

    /**
     * Robots parser
     *
     * @var RobotsParserWrap
     */
    protected $storage;

    /**
     * Статический экземпляр класса
     *
     * @var Robots
     */
    protected static $_instance;

    /**
     * Метод возврата статического экземпляра
     *
     * @uses $robots = Robots::getInstance();
     *
     * @return $this
     */
    public static function getInstance()
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
    public function host($host) : IRobot
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
    public function siteMap($siteMap) : IRobot
    {
        $this->storage->addSitemap($siteMap);
        return $this;
    }

    /**
     * Добавить GET-параметры (например, идентификаторы сессий, пользователей) или метки (напрмиер, UTM), которые не влияют на их содержимое.
     *
     * @param array $params
     * @return IRobot
     */
    public function cleanParams(array $params) : IRobot
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
    public function crawlDelay($seconds, $type = 'crawl-delay') : IRobot
    {
        switch (mb_strtolower($type))
        {
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
    public function crawlDelayForAgent(string $agent, $seconds, $type = 'crawl-delay') : IRobot
    {
        switch (mb_strtolower($type))
        {
            case 'cache':
            case 'cache-delay':
                // non-standard directive
                $directive = RobotsParserWrap::DIRECTIVE_CACHE_DELAY;
                break;
            default:
                $directive = RobotsParserWrap::DIRECTIVE_CRAWL_DELAY;
        }

        $this->storage->addRuleForAgent($agent,$seconds, $directive);
        return $this;
    }

    /**
     * Добавить правило запрета в файл robots.txt.
     * @param string|array ...$directories
     * @return IRobot
     */
    public function disallow(...$directories) : IRobot
    {
        foreach ((array) $directories as $directory)
        {
            $this->storage->addRule($this->directoryNormalize($directory), 'disallow');
        }

        return $this;
    }

    /**
     * Добавить правило запрета в файл robots.txt. для $agent
     * @param string $agent
     * @param string $directory
     * @return IRobot
     */
    public function disallowForAgent(string $agent,string $directory) : IRobot
    {
        $this->storage->addRuleForAgent($agent, $this->directoryNormalize($directory), 'Disallow');
        return $this;
    }

    /**
     * Добавить правило разрешения в файл robots.txt.
     *
     * @param string|array $directories
     * @return IRobot
     */
    public function allow(...$directories) : IRobot
    {
        foreach ((array) $directories as $directory)
        {
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
    public function allowForAgent(string $agent,string $directory) : IRobot
    {
        $this->storage->addRuleForAgent($agent, $this->directoryNormalize($directory), 'Allow');

        return $this;
    }

    /**
     * Generate the robots.txt and return content
     * @return string
     */
    public function render() : string
    {
        return $this->storage->render();
    }

    /**
     * Обновить данные robots.txt
     *
     * @param string $path
     */
    public function update(string $path = 'robots.txt')
    {
        file_put_contents($path, $this->render());
    }

    /**
     * Trailing slash
     *
     * @param string $directory
     *
     * @return string
     */
    protected function directoryNormalize(string $directory)
    {
        $directory = rtrim($directory, '/');

        return $directory;
    }

    /**
     * Get instance
     * @param string $content
     * @return RobotsParserWrap
     */
    protected function getRobotsStorage(string $content)
    {
        if( is_file($content) || filter_var($content, FILTER_VALIDATE_URL) )
        {
            $content = file_get_contents($content);
        }

        return new RobotsParserWrap($content);
    }
}