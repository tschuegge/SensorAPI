<?php
require_once __DIR__ . "/helper/RestApiTestCase.class.php";

class UserTest extends RestApiTestCase
{
    public function setUp()
    {
        $this->setupTestData();
    }

    public function tearDown()
    {
        $this->cleanupTestData();
    }

    public function testGetByName()
    {
        $response = $this->doHttpGetRequest("UnitTestUser1");
        $this->assertEquals(200, $response->code);
        $user = $response->body;
        $this->assertSame(999999998, $user->id);
        $this->assertSame("UnitTestUser1", $user->name);
        $this->assertSame("UnitTestClass", $user->class);
    }

    public function testGetByNameWithUnknownName()
    {
        $response = $this->doHttpGetRequest("UnitTestUser1Unknown");
        $this->assertEquals(404, $response->code);
        $this->assertNotContains("{", $response->body);
    }
}
