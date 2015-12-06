<?php namespace Ace\RepoMan\Exception;

use RuntimeException;

/**
 * Thrown when executing a command returns a non-zero exit code
 */
class CommandExecutionException extends RuntimeException {}
