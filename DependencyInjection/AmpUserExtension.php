<?php

namespace Ampisoft\UserBundle\DependencyInjection;


use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;


/**
 * @author Matt Holbrook-Bull <matt@ampisoft.com>
 *
 * Class AmpUserExtension
 * @package Ampisoft\UserBundle\DependencyInjection
 */
class AmpUserExtension extends Extension {

    /**
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load( array $configs, ContainerBuilder $container ) {

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        //$loader = new YamlFileLoader( new FileLocator( __DIR__ . '/../Resources/config' ) );
        $container->setParameter('amp_userbundle.templates.login', $config['templates']['login']);
        $container->setParameter('amp_userbundle.class.user', $config['classes']['user']);
        $container->setParameter('amp_userbundle.class.group', $config['classes']['group']);
        $container->setParameter('amp_userbundle.login_path', $config['paths']['login']);
        $container->setParameter('amp_userbundle.success_path', $config['paths']['success']);

    }

    /**
     * @return string
     */
    public function getAlias() {
        return 'amp_user';
    }
    
}