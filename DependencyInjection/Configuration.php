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
 * @package Ampisoft\DeployBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface{
    
    public function getConfigTreeBuilder() {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root( 'ampisoft_userbundle' );

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
                                ->defaultValue( 'AmpisoftUserbundle:security:login.html.twig' )
                            ->end()
                         ->end()
                    ->end()
                  ->end();

        return $treeBuilder;
    }

}