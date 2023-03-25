<?php  namespace DeftCMS\Components\b1tc0re\Robots;

class RobotsParserWrap extends \RobotsTxtParser
{
    /**
     * Add new rule for user agent
     * @param string $agent
     * @param string $directory
     * @param string $directoryRule
     *
     * @return $this
     */
    public function addRuleForAgent(string $agent, string $directory, string $directoryRule)
    {
        if( !array_key_exists($agent, $this->rules) )
        {
            $this->rules[$agent] = [];
        }

        foreach ($this->getRules($agent) ?? [] as $rules)
        {
            if( array_key_exists($directoryRule, $rules) )
            {
                if( !in_array($directory, $rules[$directoryRule]) )
                {
                    $this->rules[$agent][$directoryRule][] = $directory;
                }
            }
            else
            {
                $this->rules[$agent][$directoryRule][] = $directory;
            }
        }

        return $this;
    }

    /**
     * Add new rule for all user agents
     *
     * @param string $directory
     * @param string $directoryRule
     *
     * @return $this
     */
    public function addRule(string $directory, string $directoryRule)
    {
        if( count($this->rules) === 0 )
        {
            $this->rules['*'] = [];
        }

        foreach ($this->getRules() ?? [] as $userAgent => $rules)
        {
            if( array_key_exists($directoryRule, $rules) )
            {
                if( !in_array($directory, $rules[$directoryRule]) )
                {
                    $this->rules[$userAgent][$directoryRule][] = $directory;
                }
            }
            else
            {
                $this->rules[$userAgent][$directoryRule][] = $directory;
            }
        }


        return $this;
    }

    /**
     * Удалить правило
     * @param string $directory     - Адрес
     * @param string $directoryRule - Правило
     *
     * @return $this
     */
    public function removeRule(string $directory, string $directoryRule)
    {
        if( count($this->rules) === 0 )
        {
            $this->rules['*'] = [];
        }

        foreach ($this->getRules() ?? [] as $userAgent => $rules)
        {
            if( array_key_exists($directoryRule, $this->rules[$userAgent]) && $index = array_search($directory, $this->rules[$userAgent][$directoryRule]) )
            {
                unset($this->rules[$userAgent][$directoryRule][$index]);
            }
        }

        return $this;
    }

    /**
     * Set host
     *
     * @param string $host
     *
     * @return $this
     */
    public function setHost(string $host)
    {
        if (filter_var($host, FILTER_VALIDATE_URL))
        {
            if( $parsed = $this->parseURL($host) )
            {
                $this->host = $parsed['scheme'] . '://' . $parsed['host'] . '/';
            }
        }

        return $this;
    }

    /**
     * Add Sitemap
     *
     * @param string $sitemap
     *
     * @return $this
     */
    public function addSiteMap(string $sitemap)
    {
        if ( filter_var($sitemap, FILTER_VALIDATE_URL) && $parsed = $this->parseURL($sitemap) )
        {
            if( in_array( pathinfo ($parsed['path'], PATHINFO_EXTENSION ), ['xml', 'gz']) )
            {
                $sitemap = $parsed['scheme'] . '://' . $parsed['host'] . '/' . ltrim($parsed['path'],'/') ;

                if( !in_array($sitemap, $this->sitemap) )
                {
                    $this->sitemap[] = $sitemap;
                }
            }
        }

        return $this;
    }

    /**
     * Remove sitemap
     * @param string $sitemap
     * @return $this
     */
    public function removeSiteMap(string $sitemap)
    {
        if ( filter_var($sitemap, FILTER_VALIDATE_URL) && $parsed = $this->parseURL($sitemap) )
        {
            if( in_array( pathinfo ($parsed['path'], PATHINFO_EXTENSION ), ['xml', 'gz']) )
            {
                $sitemap = $parsed['scheme'] . '://' . $parsed['host'] . '/' . ltrim($parsed['path'],'/') ;

                if(($key = array_search($sitemap, $this->sitemap)) !== FALSE)
                {
                    unset($this->sitemap[$key]);
                }
            }
        }

        return $this;
    }

    /**
     * Remove all sitemap
     * @return $this
     */
    public function cleanSiteMap()
    {
        $this->sitemap = [];
        return $this;
    }

    /**
     * Extend clean params
     * @param array $params
     *
     * @return $this
     */
    public function addCleanParams(array $params)
    {
        $parameters = $this->getCleanParam();

        if( !count($parameters) )
        {
            $this->cleanparam['/*'] = [];
        }

        foreach ($this->getCleanParam() ?? [] as $index => $p )
        {
            $this->cleanparam[$index] = array_merge($this->cleanparam[$index], $params );
        }

        return $this;
    }

    /**
     * Override render add clean-params
     *
     * @param string $eol
     * @return string
     */
    public function render($eol = "\r\n")
    {
        $input = $this->getRules();
        krsort($input);
        $output = [];
        foreach ($input as $userAgent => $rules) {
            $output[] = 'User-agent: ' . $userAgent;
            foreach ($rules as $directive => $value) {
                // Not multibyte
                $directive = ucfirst($directive);
                if (is_array($value)) {
                    // Shorter paths later
                    usort($value, function ($a, $b) {
                        return mb_strlen($a) < mb_strlen($b) ? 1 : 0;
                    });
                    foreach ($value as $subValue) {
                        $output[] = $directive . ': ' . $subValue;
                    }
                } else {
                    $output[] = $directive . ': ' . $value;
                }
            }
            $output[] = '';
        }

        $host = $this->getHost();
        if ($host !== null) {
            $output[] = 'Host: ' . $host;
        }

        $sitemaps = $this->getSitemaps();
        foreach ($sitemaps as $sitemap) {
            $output[] = 'Sitemap: ' . $sitemap;
        }

        foreach ($this->getCleanParam() ?? [] as $path)
        {
            $output[] = 'Clean-param: ' . implode('&', $path);
        }


        $output[] = '';
        return implode($eol, $output);
    }
}