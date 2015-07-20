<?php
/**
 * @author timrodger
 * Date: 14/07/15
 */
use Sce\RepoMan\Domain\GitRepo;
use Sce\RepoMan\Domain\Composer;
use Sce\RepoMan\Configuration;

require_once(__DIR__.'/../vendor/autoload.php');
require_once(__DIR__.'/../config.php');

$composers = [];
//$repositories = [];
$dir = __DIR__ . '/../tmp';

$config = new Configuration($dir);

foreach ($config->getRepositoryNames() as $uri){
    $repository = new GitRepo($uri, $dir);

    print "updating $uri\n";

    $repository->update();

    $composer_json = $repository->getFile('composer.json');
    $composer_lock = $repository->getFile('composer.lock');

    $composer = new Composer(json_decode($composer_json, true), json_decode($composer_lock, true));
    //$repositories[$uri] = $repository;
    $composers[$uri] = $composer;
}

$dependencies = [];

foreach($composers as $uri => $composer) {

    $lock_dependencies = $composer->getLockDependencies();

    foreach($lock_dependencies as $name => $data){

        $version = $data['version'];
        $date = $data['time'];

        if (!isset($dependencies[$name])){
            $dependencies[$name] = [];
        }
        if (!isset($dependencies[$name][$version])){
            $dependencies[$name][$version] = [];
        }

        // store the uri, configured version and date here, not just the uri
        $configured_version = $composer->getDependencyVersion($name);
        $date = $composer->getLockDate($name);

        $dependencies[$name][$version] []= ['uri' => $uri, 'config_version' => $configured_version, 'date' => $date];
    }
}

foreach($dependencies as $name => &$deps){
    ksort($deps, SORT_NATURAL);
}

$output = [];
$output []= ['Library', 'Version', 'Used By', 'Configured Version', 'Last Updated'];

foreach($dependencies as $name => $deps){

    $first_name = true;

    foreach($deps as $version => $client_data){

        $first_version = true;

        foreach($client_data as $client) {
            if ($first_name) {
                $output [] = [$name, $version, $client['uri'], $client['config_version'], $client['date']];
                $first_name = false;
                $first_version = false;
            } elseif ($first_version){
                $output [] = ['', $version, $client['uri'], $client['config_version'], $client['date']];
                $first_version = false;
            } else {
                $output [] = ['', '', $client['uri'], $client['config_version'], $client['date']];
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
