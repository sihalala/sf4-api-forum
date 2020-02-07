<?php


namespace App\Test;


use ApiPlatform\Core\EventListener\EventPriorities;
use App\EventSubscriber\AuthoredEntitySubscriber;
use App\EventSubscriber\EmptyBodySubscriber;
use App\Exception\EmptyBodyException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class EmptyBodySubscriberTest extends TestCase
{
    public function testConfiguration(){
        $result = EmptyBodySubscriber::getSubscribedEvents();
        $this->assertArrayHasKey(KernelEvents::REQUEST,$result);
        $this->assertEquals(['handleEmptyBody',EventPriorities::POST_DESERIALIZE],
            $result[KernelEvents::REQUEST]);
    }

    /**
     * @dataProvider providerHandleEmptyBody
     */
    public function testHandleEmptyBody(string $method,$data){
        $this->getEventMock($method,$data,true);
    }

    public function testMethodNotAllow(){
        $this->getEventMock('GET',[123],false);

    }
    private function getEventMock(string $method , $data, bool $allow){
        $requestMock = $this->getMockBuilder(Request::class)
            ->getMock();
        $requestMock->expects($this->once())->method('getMethod')
            ->willReturn($method);

        $eventMock = $this->getMockBuilder(RequestEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
        $eventMock->expects($allow ? $this->exactly(2) : $this->once())->method('getRequest')
            ->willReturn($requestMock);

        $requestMock->expects($allow ? $this->once() : $this->never())->method('get')
            ->willReturn($data);
        if(!isset($data)){
            $this->expectException(EmptyBodyException::class);
        }
        (new EmptyBodySubscriber())->handleEmptyBody($eventMock);
    }

    public function providerHandleEmptyBody():array
    {
        return [
            ['POST',[123]],
            ['POST',null]
        ];
    }
}