<?php
namespace RocketTheme\Toolbox\File;

use Symfony\Component\Yaml\Exception\DumpException;
use Symfony\Component\Yaml\Exception\ParseException;
use \Symfony\Component\Yaml\Yaml as YamlParser;

/**
 * Implements YAML File reader.
 *
 * @package RocketTheme\Toolbox\File
 * @author RocketTheme
 * @license MIT
 */
class YamlFile extends File
{
    /**
     * @var array|File[]
     */
    static protected $instances = array();

    /**
     * Constructor.
     */
    protected function __construct()
    {
        parent::__construct();

        $this->extension = '.yaml';
    }

    /**
     * Check contents and make sure it is in correct format.
     *
     * @param array $var
     * @return array
     */
    protected function check($var)
    {
        return (array) $var;
    }

    /**
     * Encode contents into RAW string.
     *
     * @param string $var
     * @return string
     * @throws DumpException
     */
    protected function encode($var)
    {
        return (string) YamlParser::dump($var, $this->setting('inline', 5), $this->setting('indent', 2), true, false);
    }

    /**
     * Decode RAW string into contents.
     *
     * @param string $var
     * @return array mixed
     * @throws ParseException
     */
    protected function decode($var)
    {
        // Try native PECL Yaml PHP extension first if available, otherwise fall back to symfony\Yaml parser
        if (function_exists('yaml_parse')) {
            if (strpos($var, ' @'))
                $data = preg_replace("/ (@[\w\.\-]*)/", " '\${1}'", $var); // Fix illegal @ start character issue
            else
                $data = $var;
            $data = @yaml_parse("---\n" . $data . "\n...");
            if ($data)
                return $data;
            // else continue with symfony\Yaml parser if there have been parse errors...
        }

        return (array) YamlParser::parse($var);
    }
}
