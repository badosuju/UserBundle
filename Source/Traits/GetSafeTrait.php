<?php
namespace AmpUserBundle\Source\Traits;

use Symfony\Component\Intl\Exception\MethodNotImplementedException;

/**
 * @author Matt Holbrook-Bull <matt@ampisoft.com>
 *
 * Class GetSafeTrait
 *
 * @package AmpUserBundle\Source\Traits
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
            return htmlspecialchars( $this->$method );
        } else {
            throw new MethodNotImplementedException( "'Error: method '$method' does not exist!" );
        }
    }
}

