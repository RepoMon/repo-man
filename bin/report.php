<?php
/**
 * @author timrodger
 * Date: 14/07/15
 */
use Sce\Repo\GitRepo;
use Sce\Repo\Composer;

require_once(__DIR__.'/../vendor/autoload.php');
require_once(__DIR__.'/../config.php');

$composers = [];
$repositories = [];
$dir = __DIR__ . '/../tmp';

$config = new Config();

foreach ($config->getRepositories() as $uri){
    $repository = new GitRepo($uri, $dir);

    print "updating $uri\n";

    $repository->update();

    $composer_json = $repository->getFile('composer.json');
    $composer_lock = $repository->getFile('composer.lock');

    $composer = new Composer(json_decode($composer_json, true), json_decode($composer_lock, true));
    $repositories[$uri] = $repository;
    $composers[$uri] = $composer;
}

$dependencies = [];

foreach($composers as $uri => $composer) {

    $lock_dependencies = $composer->getLockDependencies();

    foreach($lock_dependencies as $name => $version){
        if (!isset($dependencies[$name])){
            $dependencies[$name] = [];
        }
        if (!isset($dependencies[$name][$version])){
            $dependencies[$name][$version] = [];
        }
        $dependencies[$name][$version] []= $uri;
    }
}

foreach($dependencies as $name => &$deps){
    ksort($deps, SORT_NATURAL);
}

$output = [];
$output []= ['Library', 'Version', 'Used by'];

foreach($dependencies as $name => $deps){

    $first_name = true;

    foreach($deps as $version => $uris){

        $first_version = true;

        foreach($uris as $uri) {
            if ($first_name) {
                $output [] = [$name, $version, $uri];
                $first_name = false;
                $first_version = false;
            } elseif ($first_version){
                $output [] = ['', $version, $uri];
                $first_version = false;
            } else {
                $output [] = ['', '', $uri];
            }
        }
    }

    $output []= ['','',''];
}

$out = fopen(__DIR__. '/report.csv', 'w+');

foreach($output as $line) {
    fputcsv($out, $line);
}
fclose($out);
