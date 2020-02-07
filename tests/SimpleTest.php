<?php
namespace App\Test;

use PHPUnit\Framework\TestCase;

class SimpleTest extends TestCase
{
    public function testAddtion(){
        $array = [
            'key' => 'value'
        ];
        $this->assertEquals(5,3+2,'Five was expected to equal 2 +3');
        $this->assertTrue(true);
        $this->assertArrayHasKey('key',$array);
        $this->assertCount(1,$array);
    }
}