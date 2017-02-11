<?php
/**
 * @author Matt Holbrook-Bull <matt@ampisoft.com>
 * 12/02/16
 */
namespace Ampisoft\UserBundle\Tests\Security;


use Ampisoft\UserBundle\Entity\AbstractUser;
use Ampisoft\UserBundle\Security\ApiTokenAuthenticator;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserProviderInterface;


class ApiTokenAuthenticatorTest extends \PHPUnit_Framework_TestCase {

    private $em;
    private $userRepository;
    private $user;

    public function setUp() {
        $this->user = $this->getMock( TestUser::class );
        $this->user->method( 'getApiToken' )
                   ->will( $this->returnValue( 'abcdef12345' ) );

        $this->userRepository = $this->getMockBuilder( EntityRepository::class )
                                     ->disableOriginalConstructor()
                                     ->getMock();

        $this->userRepository->method( 'findOneBy' )
                             ->will( $this->returnValue( $this->user ) );

        $this->em = $this->getMockBuilder( ObjectManager::class )
                         ->disableOriginalConstructor()
                         ->getMock();

        $this->em->method( 'getRepository' )
                 ->will( $this->returnValue( $this->userRepository ) );
    }

    public function testGetCredentials() {
        $authenticator = new ApiTokenAuthenticator( $this->em, TestUser::class );
        $request = new Request();
        $request->headers->set( 'x-token', 'abcdef12345' );
        $this->assertEquals( 'abcdef12345', $authenticator->getCredentials( $request ) );
    }

    public function testOnAuthenticationFailure() {
        $authenticator = new ApiTokenAuthenticator( $this->em, TestUser::class );

        $request = new Request();
        $e = new AuthenticationException();
        $response = $authenticator->onAuthenticationFailure( $request, $e);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(401, $response->getStatusCode());
    }
}

class TestUser extends AbstractUser {

}

