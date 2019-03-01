<?php  namespace DeftCMS\Components\b1tc0re\Robots;

use Closure;

/**
 * Interface IRobot
 * @package DeftCMS\Components\b1tc0re\Robots
 */
interface IRobot
{
    /**
     * Добавить комментарий к robots.txt.
     *
     * @param string|array $comment
     * @return IRobot
     *
     * @throws ExceptionRobot
     */
    public function comment(...$comment) : IRobot;

    /**
     * Добавить хост в файл robots.txt.
     *
     * @param string $host
     * @return IRobot
     */
    public function host($host) : IRobot;

    /**
     * Добавить правило запрета в файл robots.txt.
     *
     * @param string|array $directories
     * @return IRobot
     *
     * @throws ExceptionRobot
     */
    public function disallow(...$directories) : IRobot;

    /**
     * Добавить правило разрешения в файл robots.txt.
     *
     * @param string|array $directories
     * @return IRobot
     *
     * @throws ExceptionRobot
     */
    public function allow(...$directories) : IRobot;

    /**
     * Добавить User-agent в файл robots.txt.
     *
     * @param string $userAgent
     * @return IRobot
     */
    public function userAgent($userAgent) : IRobot;


    /**
     * Добавить ссылку на файл Sitemap в файл robots.txt.
     *
     * @param string|array $sitemap
     * @return IRobot
     *
     * @throws ExceptionRobot
     */
    public function sitemap($sitemap) : IRobot;


    /**
     * Добавить разделителя в файл robots.txt.
     *
     * @param int $num
     * @return IRobot
     */
    public function spacer(int $num = 1) : IRobot;


    /**
     * Выполните обратный вызов для каждого пакетного агента.
     *
     * @param callable| Closure $closure
     * @return IRobot
     */
    public function each(\Closure $closure) : IRobot;

    /**
     * Создает файл с записанами данными
     *
     * @param string|null $path
     *
     * @return void
     */
    public function create(string $path = "robots.txt");

    /**
     * Вывод сгенерированных данных в файл robots.txt.
     *
     * @return string
     */
    public function render() : string;
}