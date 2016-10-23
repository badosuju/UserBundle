<?php

namespace Ampisoft\UserBundle\DependencyInjection;


use AppBundle\Entity\Group;
use AppBundle\Entity\User;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;


/**
 * @author Matt Holbrook-Bull <matt@ampisoft.com>
 *
 * Class Configuration
 * @package Ampisoft\UserBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface{
    
    public function getConfigTreeBuilder() {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root( 'amp_user' );

        $rootNode
                  ->children()
                    ->arrayNode( 'classes' )
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode( 'user' )
                                ->defaultValue( '/' . User::class)
                            ->end()
                            ->scalarNode( 'group' )
                                ->defaultValue( '/' . Group::class )
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('templates')
                        ->addDefaultsIfNotSet()
                         ->children()
                            ->scalarNode('login')
                                ->defaultValue( 'AmpUserBundle:security:login.html.twig' )
                            ->end()
                         ->end()
                    ->end()
                    ->arrayNode( 'paths' )
                        ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode( 'login' )
                                ->defaultValue( 'security_login' )->end()
                                ->scalarNode('success')->defaultValue('homepage')->end()
                            ->end()
                        ->end()
                    ->end()
                  ->end();

        return $treeBuilder;
    }

}