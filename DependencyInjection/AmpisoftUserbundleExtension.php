<?php

namespace Ampisoft\UserBundle\DependencyInjection;


use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;


/**
 * @author Matt Holbrook-Bull <matt@ampisoft.com>
 *
 * Class AmpisoftUserBundleExtension
 * @package Ampisoft\UserBundle\DependencyInjection
 */
class AmpisoftUserbundleExtension extends Extension {

    /**
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load( array $configs, ContainerBuilder $container ) {

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        //$loader = new YamlFileLoader( new FileLocator( __DIR__ . '/../Resources/config' ) );
        $container->setParameter('ampisoft_userbundle.templates.login', $config['templates']['login']);
        $container->setParameter('ampisoft_userbundle.class.user', $config['classes']['user']);
        $container->setParameter('ampisoft_userbundle.class.group', $config['classes']['group']);
    }

    /**
     * @return string
     */
    public function getAlias() {
        return 'ampisoft_userbundle';
    }
    
}