<?php


namespace App\Test;


use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\BlogPost;
use App\Entity\User;
use App\EventSubscriber\AuthoredEntitySubscriber;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AuthoredEntitySubscriberTest extends TestCase
{
    public function testConfiguration(){
        $result = AuthoredEntitySubscriber::getSubscribedEvents();
        $this->assertArrayHasKey(KernelEvents::VIEW,$result);
        $this->assertEquals(['getAuthenticatedUser',EventPriorities::PRE_WRITE],
            $result[KernelEvents::VIEW]);
    }

    /**
     * @dataProvider providerSetAuthorCall
     */
    public function testSetAuthorCall(string $className, bool $shouldCallSetAuthor,string $method){
        $entityMock = $this->getEntityMock($className,$shouldCallSetAuthor);

        //function nay dung de test cho toan bo class AuthoredEntitySubscriber luon
        // Ca class AuthorEntitySubscriber goi getUser() va getToken() 1 lan nen ta goi once()
        $tokenStorageMock = $this->getTockenStorageMock();

        $eventMock = $this->getEventMock($method, $entityMock);
        (new AuthoredEntitySubscriber($tokenStorageMock))->getAuthenticatedUser($eventMock);
    }

    public function testTokenStorageIsNull(){
        $entityMock = $this->getEntityMock(BlogPost::class,true);

        //function nay dung de test cho toan bo class AuthoredEntitySubscriber luon
        // Ca class AuthorEntitySubscriber goi getUser() va getToken() 1 lan nen ta goi once()
        $tokenStorageMock = $this->getTockenStorageMock(true);

        $eventMock = $this->getEventMock('POST', $entityMock);
        (new AuthoredEntitySubscriber($tokenStorageMock))->getAuthenticatedUser($eventMock);
    }

    public function providerSetAuthorCall():array
    {
        return [
            [BlogPost::class, true, 'POST'],
            [BlogPost::class, false, 'GET'],
            ['NonExisting', false, 'POST']
        ];
    }

    /**
     * @return MockObject|TokenStorageInterface
     */
    private function getTockenStorageMock(bool $isTokenNull = false): MockObject
    {
// 2 mock o duoi nay dung de test la function getToken() va getUser() duoc goi 1 lan
        $tokenMock = $this->getMockBuilder(TokenInterface::class)
            ->getMockForAbstractClass();
        $tokenMock->expects($isTokenNull ? $this->never() : $this->once())
            ->method('getUser')
            ->willReturn(new User());


        $tokenStorageMock = $this->getMockBuilder(TokenStorageInterface::class)
            ->getMockForAbstractClass(); // for Abstract and Interface , For nomall class we use getMock()
        $tokenStorageMock->expects($isTokenNull ? $this->never() : $this->once())
            ->method('getToken')
            ->willReturn( $isTokenNull ? null : $tokenMock);
        return $tokenStorageMock; // o day thi cung ta se bat tokenStorageMock goi function getToken va return 1 gia tri nao do
        // Boi vi ham getToken() tra ve 1 object TokenInterface nen chung
        // ta lai phai tiep tuc tao mock cho token interface , sau cung
        // thi chung ta moi co the tra ve 1 instance User rong
    }

    /**
     * @return MockObject|GetResponseForControllerResultEvent
     */
    private function getEventMock(string $method , $controllerResult): MockObject
    {
        $requestMock = $this->getMockBuilder(Request::class)
            ->getMock();
        $requestMock->expects($this->once())
            ->method('getMethod')
            ->willReturn($method);

        $eventMock = $this->getMockBuilder(GetResponseForControllerResultEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
        $eventMock->expects($this->once())->method("getControllerResult")
            ->willReturn($controllerResult);
        $eventMock->expects($this->once())
            ->method('getRequest')
            ->willReturn($requestMock);
        return $eventMock;
    }

    /**
     * @return MockObject
     */
    private function getEntityMock($className, $shouldCallSetAuthor): MockObject
    {
        $entityMock = $this->getMockBuilder($className)
            ->setMethods(['setAuthor'])
            ->getMock();

        $entityMock->expects($shouldCallSetAuthor ? $this->once() : $this->never())
            ->method('setAuthor');
        return $entityMock;
    }
}