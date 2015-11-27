<?php
/**
 * Created by PhpStorm.
 * User: Tibi
 * Date: 2015.11.20.
 * Time: 13:24
 */
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Yaml\Yaml;

require_once "../../autoload.php";

chdir(dirname(dirname(dirname(__DIR__))));
$settings = YAML::parse(file_get_contents($argv[0]));

$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration(array($settings['entity_path']), $isDevMode);

// database configuration parameters
$conn = $settings['Database'];

// obtaining the entity manager
$entityManager = EntityManager::create($conn, $config);
return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);