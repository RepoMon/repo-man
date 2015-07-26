<?php namespace Sce\RepoMan\Domain;

/**
 * @author timrodger
 * Date: 26/07/15
 */
class CommandLine
{
    /**
     * @var string
     */
    private $directory;

    public function __construct($directory)
    {
        $this->directory = $directory;
    }

    /**
     * @param $cmd string
     * @return array
     */
    public function exec($cmd)
    {
        if (is_dir($this->directory)) {
            chdir($this->directory);
            exec($cmd, $output, $return);
            if ($return !== 0) {
                throw new CommandExecutionException("Exit code of '$cmd' was '$return''");
            }
            return $output;
        } else {
            throw new DirectoryNotFoundException("{$this->directory} is missing");
        }
    }
}