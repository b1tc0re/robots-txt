<?php  namespace DeftCMS\Components\b1tc0re\Robots;

use Closure;

class Robot implements IRobot
{
    /**
     * Линий файла robot.txt
     *
     * @var array
     */
    protected $storage   =   [
        //
    ];

    /**
     * Статический экземпляр класса
     *
     * @var Robot
     */
    protected static $_instance;

    /**
     * Метод возврата статического экземпляра
     *
     * @uses $robots = Robot::getInstance();
     *
     * @return IRobot
     */
    public static function getInstance() : IRobot
    {
        self::$_instance || (self::$_instance = new static());
        return self::$_instance;
    }

    /**
     * Добавить комментарий к robots.txt.
     *
     * @param string|array $comment
     * @return IRobot
     *
     * @throws ExceptionRobot
     */
    public function comment(...$comment): IRobot
    {
        $this->addLines($comment, "# ");
        return $this;
    }

    /**
     * Добавить хост в файл robots.txt.
     *
     * @param string $host
     * @return IRobot
     */
    public function host($host): IRobot
    {
        $this->addLine("Host: $host");

        return $this;
    }

    /**
     * Добавить правило запрета в файл robots.txt.
     *
     * @param string|array $directories
     * @return IRobot
     *
     * @throws ExceptionRobot
     */
    public function disallow(...$directories): IRobot
    {
        $this->addRuleLine($directories, 'Disallow');

        return $this;
    }

    /**
     * Добавить правило разрешения в файл robots.txt.
     *
     * @param string|array $directories
     * @return IRobot
     *
     * @throws ExceptionRobot
     */
    public function allow(...$directories): IRobot
    {
        $this->addRuleLine($directories, 'Allow');
        return $this;
    }

    /**
     * Добавить User-agent в файл robots.txt.
     *
     * @param string $userAgent
     * @return IRobot
     */
    public function userAgent($userAgent): IRobot
    {
        $this->addLine("User-agent: $userAgent");

        return $this;
    }

    /**
     * Добавить ссылку на файл Sitemap в файл robots.txt.
     *
     * @param string|array $sitemap
     * @return IRobot
     *
     * @throws ExceptionRobot
     */
    public function sitemap($sitemap): IRobot
    {
        if( is_array($sitemap) )
        {
            $this->addLines($sitemap, "Sitemap: ");
        }
        else
        {
            $this->addLine("Sitemap: ".$sitemap);
        }

        return $this;
    }

    /**
     * Добавить разделителя в файл robots.txt.
     *
     * @param int $num
     * @return IRobot
     */
    public function spacer(int $num = 1): IRobot
    {
        for ($i = 0; $i <= $num; $i++)
        {
            $this->addLine(null);
        }

        return $this;
    }

    /**
     * Выполните обратный вызов для каждого пакетного агента.
     *
     * @param callable| Closure $closure
     * @return IRobot
     */
    public function each(\Closure $closure): IRobot
    {
        if($closure instanceof \Closure)
        {
            $closure($this);
        }

        return $this;
    }

    /**
     * Создает файл с записанами данными
     *
     * @param string|null $path
     *
     * @return void
     */
    public function create(string $path = "robots.txt")
    {
        $storage = $this->getOriginal();
        $this->storage = array_merge($storage, $this->storage);

        file_exists($path) && unlink($path);
        file_put_contents($path, $this->render());
    }

    /**
     * Вывод сгенерированных данных в файл robots.txt.
     *
     * @return string
     */
    public function render(): string
    {
        return implode(PHP_EOL, $this->storage);
    }

    /**
     * Добавить новое правил в файл robots.txt.
     *
     * @param string|array $directories
     * @param string $rule
     *
     * @throws ExceptionRobot
     */
    protected function addRuleLine($directories, $rule)
    {
        $this->isEmpty($directories);

        foreach ((array) $directories as $directory)
        {
            $this->addLine("$rule: $directory");
        }
    }

    /**
     * Получить текушие данные
     * @param string $path
     * @return array
     */
    protected function getOriginal(string $path = "robots.txt")
    {
        if( file_exists($path) )
        {
            $content = file_get_contents($path);
            return explode(PHP_EOL, $content);
        }

        return [];
    }

    /**
     * Добавить новую строки в robots.txt.
     *
     * @param string $line
     */
    protected function addLine($line)
    {
        $this->storage[] = (string) $line;
    }

    /**
     * Добавление нескольких строк в файл robots.txt.
     *
     * @param string|array $lines
     * @param null $prefix
     * @return IRobot
     * @throws ExceptionRobot
     */
    protected function addLines($lines, $prefix = null) : IRobot
    {
        $this->isEmpty($lines);

        foreach ((array) $lines as $line)
        {
            if($prefix != null)
            {
                $this->addLine($prefix.$line);
            }
            else
            {
                $this->addLine($line);
            }
        }

        return $this;
    }

    /**
     * Проверить наличие данных
     *
     * @param null $var
     * @return void
     * @throws ExceptionRobot
     */
    protected function isEmpty($var)
    {
        if( $this->fnIsEmpty($var) )
        {
            throw new ExceptionRobot("The parameter must not be empty");
        }
    }

    /**
     * Проверить наличие данных
     * @param mixed $var
     * @return bool
     */
    protected  function fnIsEmpty($var)
    {
        if (is_array($var))
        {
            foreach ($var as $k => $v)
            {
                if (empty($v))
                {
                    unset($var[$k]);
                    continue;
                }

                if (is_array($v) && $this->fnIsEmpty($v))
                {
                    unset($var[$k]);
                }
            }
        }

        return empty($var);
    }
}