<?php
namespace Ampisoft\UserBundle\Source\Traits;


use Symfony\Component\Intl\Exception\MethodNotImplementedException;


/**
 * @author Matt Holbrook-Bull <matt@ampisoft.com>
 *
 * Class GetSafeTrait
 *
 * @package Ampisoft\UserBundle\Source\Traits
 */
trait GetSafeTrait {

    /**
     * @param $name
     * @param $attributes
     * @return null|string
     */
    public function __call( $name, $attributes ) {
        if ( substr( $name, 0 ) !== 'getSafe' ) {
            return null;
        }
        $method = 'get' . ucwords( str_replace( 'getSafe', '', $name ) );
        if ( method_exists( $this, $method ) ) {
            $returnString = $this->$method;
            if ( is_string( $returnString ) ) {
                return htmlspecialchars( $returnString );
            }

            return null;
        } else {
            throw new MethodNotImplementedException( "'Error: method '$method' does not exist!" );
        }
    }
}

