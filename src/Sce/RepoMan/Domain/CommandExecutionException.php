<?php namespace Sce\RepoMan\Domain;

/**
 * Thrown when executing a command returns a non-zero exit code
 */
class CommandExecutionException extends \RuntimeException {}
