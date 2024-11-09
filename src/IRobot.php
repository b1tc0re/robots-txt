<?php namespace DeftCMS\Components\b1tc0re\Robots;

use Closure;

/**
 * Interface IRobot
 * @package DeftCMS\Components\b1tc0re\Robots
 */
interface IRobot
{
    /**
     * Добавить хост в файл robots.txt.
     *
     * @param string $host
     * @return IRobot
     */
    public function host(string $host): IRobot;

    /**
     * Добавить ссылку на файл SiteMap в файл robots.txt.
     *
     * @param string|array $siteMap
     * @return IRobot
     *
     */
    public function siteMap(...$siteMap): IRobot;

    /**
     * Удалить ссылку на файл SiteMap в файл robots.txt.
     *
     * @param string|array $siteMap
     * @return IRobot
     */
    public function removeSiteMap(...$siteMap): IRobot;

    /**
     * Удалить все ссылки на файл SiteMap в файл robots.txt.
     *
     * @return IRobot
     */
    public function cleanSiteMap(): IRobot;

    /**
     * Добавить GET-параметры (например, идентификаторы сессий, пользователей) или метки (например, UTM), которые не влияют на их содержимое.
     *
     * @param array $params
     * @return $this
     */
    public function cleanParams(array $params): IRobot;

    /**
     * Задать поисковым роботу минимальный период времени (в секундах)
     *
     * @param float|int $seconds
     * @param string $type
     * @return IRobot
     */
    public function crawlDelay(float|int $seconds, string $type = 'crawl-delay'): IRobot;

    /**
     * Задать поисковому роботу минимальный период времени (в секундах)
     * @param string $agent
     * @param int|float $seconds
     * @param string $type
     * @return IRobot
     */
    public function crawlDelayForAgent(string $agent, int|float $seconds, string $type = 'crawl-delay'): IRobot;

    /**
     * Добавить правило запрета в файл robots.txt.
     * @param string|array ...$directories
     * @return IRobot
     */
    public function disallow(...$directories): IRobot;

    /**
     * Добавить правило запрета в файл robots.txt. Для $agent
     * @param string $agent
     * @param string $directory
     * @return IRobot
     */
    public function disallowForAgent(string $agent, string $directory): IRobot;

    /**
     * Добавить правило разрешения в файл robots.txt.
     *
     * @param string|array $directories
     * @return IRobot
     */
    public function allow(...$directories): IRobot;

    /**
     * Добавить правило разрешения в файл robots.txt. для $agent
     * @param string $agent
     * @param string $directory
     * @return IRobot
     */
    public function allowForAgent(string $agent, string $directory): IRobot;

    /**
     * Generate the robots.txt and return content
     * @return string
     */
    public function render(): string;

    /**
     * Обновить данные robots.txt
     *
     * @param string $path
     * @return void
     */
    public function update(string $path = 'robots.txt'): void;
}