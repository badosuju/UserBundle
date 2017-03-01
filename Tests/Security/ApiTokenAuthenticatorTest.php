<?php
/**
 * @author Matt Holbrook-Bull <matt@ampisoft.com>
 * 12/02/16
 */
namespace Ampisoft\UserBundle\Tests\Security;


use Ampisoft\UserBundle\Entity\AbstractUser;
use Ampisoft\UserBundle\Security\ApiTokenAuthenticator;
use Ampisoft\UserBundle\Tests\BaseSecurityTestCase;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiTokenAuthenticatorTest extends BaseSecurityTestCase
{

    public function setUp() {
        $this->user = $this->createMock( TestUser::class );
        $this->user->method( 'getApiToken' )
                   ->will( $this->returnValue( 'abcdef12345' ) );

        $this->userRepository = $this->getMockBuilder( EntityRepository::class )
                                     ->disableOriginalConstructor()
                                     ->getMock();

        parent::setUp();
    }

    public function testGetCredentials() {
        $authenticator = new ApiTokenAuthenticator( $this->em, TestUser::class );
        $request = new Request();
        $request->headers->set( 'x-token', 'abcdef12345' );
        self::assertEquals( 'abcdef12345', $authenticator->getCredentials( $request ) );
    }

    public function testOnAuthenticationFailure() {
        $authenticator = new ApiTokenAuthenticator( $this->em, TestUser::class );

        $request = new Request();
        $e = new AuthenticationException();
        $response = $authenticator->onAuthenticationFailure( $request, $e);
        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertEquals(401, $response->getStatusCode());
    }
}

class TestUser extends AbstractUser {

}

