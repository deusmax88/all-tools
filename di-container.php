<?php
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

// init service container
$containerBuilder = new ContainerBuilder();

// init yaml file loader
$loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__));

// load services from the yaml file
$loader->load('services.yaml');