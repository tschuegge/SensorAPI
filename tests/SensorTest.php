<?php
require_once __DIR__ . "/helper/RestApiTestCase.class.php";

class SensorTest extends RestApiTestCase
{
    public function setUp()
    {
        $this->setupTestData();
    }

    public function tearDown()
    {
        $this->cleanupTestData();
    }

    public function testGetAll()
    {
        $response = $this->doHttpGetRequest("UnitTestUser1/sensor");
        $this->assertEquals(200, $response->code);
        $sensors = $response->body;
        $this->assertCount(2, $sensors);
        $this->assertSame(999999996, $sensors[0]->id);
        $this->assertSame("UnitTestSensor1User1", $sensors[0]->name);
        $this->assertSame(999999998, $sensors[0]->userid);
        $this->assertSame(999999997, $sensors[1]->id);
        $this->assertSame("UnitTestSensor2User1", $sensors[1]->name);
        $this->assertSame(999999998, $sensors[1]->userid);
    }

    public function testGetById()
    {
        $response = $this->doHttpGetRequest("UnitTestUser1/sensor/999999997");
        $this->assertEquals(200, $response->code);
        $sensor = $response->body;
        $this->assertSame(999999997, $sensor->id);
        $this->assertSame("UnitTestSensor2User1", $sensor->name);
        $this->assertSame(999999998, $sensor->userid);
    }

    public function testGetByIdOfOtherUser()
    {
        $response = $this->doHttpGetRequest("UnitTestUser1/sensor/999999998");
        $this->assertEquals(404, $response->code);
        $this->assertNotContains("{", $response->body);
        $this->assertNotContains("UnitTestUser2", $response->body);
    }

    public function testGetByIdUnknown()
    {
        $response = $this->doHttpGetRequest("UnitTestUser1/sensor/1234567890");
        $this->assertEquals(404, $response->code);
        $this->assertNotContains("{", $response->body);
    }

    public function testAdd()
    {
        $response = $this->doHttpPostRequest("UnitTestUser1/sensor", '{"id":null,"name":"TestAddSensor","userid":null}');
        $this->assertEquals(204, $response->code);

        $requestPath = substr($response->headers['Location'], strlen(self::$baseurl));
        $id = (int)substr($response->headers['Location'], strrpos($response->headers['Location'], "/") + 1);

        $response = $this->doHttpGetRequest($requestPath);
        $sensor = $response->body;
        $this->assertSame($id, $sensor->id);
        $this->assertSame("TestAddSensor", $sensor->name);
        $this->assertSame(999999998, $sensor->userid);
    }

    public function testAddWithWrongIdAndUserid()
    {
        $response = $this->doHttpPostRequest("UnitTestUser1/sensor", '{"id":1234567890,"name":"TestAddSensor","userid":1234567890}');
        $this->assertEquals(204, $response->code);

        $requestPath = substr($response->headers['Location'], strlen(self::$baseurl));
        $id = (int)substr($response->headers['Location'], strrpos($response->headers['Location'], "/") + 1);

        $response = $this->doHttpGetRequest($requestPath);
        $sensor = $response->body;
        $this->assertSame($id, $sensor->id);
        $this->assertSame("TestAddSensor", $sensor->name);
        $this->assertSame(999999998, $sensor->userid);
    }

    public function testAddInvalidJson()
    {
        $response = $this->doHttpPostRequest("UnitTestUser1/sensor", '{id:null,"name":"TestAddSensor","userid":null}');
        $this->assertEquals(400, $response->code);
        $response = $this->doHttpPostRequest("UnitTestUser1/sensor", '{"id:null,"name":"TestAddSensor","userid":null}');
        $this->assertEquals(400, $response->code);
        $response = $this->doHttpPostRequest("UnitTestUser1/sensor", '{\'id\':null,"name":"TestAddSensor","userid":null}');
        $this->assertEquals(400, $response->code);
        $response = $this->doHttpPostRequest("UnitTestUser1/sensor", '{"name":"TestAddSensor","userid":null}');
        $this->assertEquals(400, $response->code);
    }

    public function testEdit()
    {
        $response = $this->doHttpPutRequest("UnitTestUser1/sensor/999999996", '{"id":999999996,"name":"UnitTestSensor1User1Edited","userid":999999998}');
        $this->assertEquals(204, $response->code);

        $response = $this->doHttpGetRequest("UnitTestUser1/sensor/999999996");
        $sensor = $response->body;
        $this->assertSame(999999996, $sensor->id);
        $this->assertSame("UnitTestSensor1User1Edited", $sensor->name);
        $this->assertSame(999999998, $sensor->userid);
    }

    public function testEditWithWrongId()
    {
        $response = $this->doHttpPutRequest("UnitTestUser1/sensor/999999996", '{"id":999999997,"name":"UnitTestSensor1User1Edited","userid":999999998}');
        $this->assertEquals(400, $response->code);

        $response = $this->doHttpGetRequest("UnitTestUser1/sensor/999999996");
        $sensor = $response->body;
        $this->assertSame(999999996, $sensor->id);
        $this->assertSame("UnitTestSensor1User1", $sensor->name);
        $this->assertSame(999999998, $sensor->userid);
    }

    public function testEditWithWrongUserid()
    {
        $response = $this->doHttpPutRequest("UnitTestUser1/sensor/999999996", '{"id":999999997,"name":"UnitTestSensor1User1Edited","userid":999999999}');
        $this->assertEquals(400, $response->code);

        $response = $this->doHttpGetRequest("UnitTestUser1/sensor/999999996");
        $sensor = $response->body;
        $this->assertSame(999999996, $sensor->id);
        $this->assertSame("UnitTestSensor1User1", $sensor->name);
        $this->assertSame(999999998, $sensor->userid);
    }

    public function testEditOfOtherUser()
    {
        $response = $this->doHttpPutRequest("UnitTestUser1/sensor/999999998", '{"id":999999998,"name":"UnitTestSensor1User2Edited","userid":999999998}');
        $this->assertEquals(404, $response->code);
    }

    public function testEditUnknown()
    {
        $response = $this->doHttpPutRequest("UnitTestUser1/sensor/1234567890", '{"id":1234567890,"name":"UnitTestSensor1User2Edited","userid":999999998}');
        $this->assertEquals(404, $response->code);
    }

    public function testEditInvalidJson()
    {
        $response = $this->doHttpPutRequest("UnitTestUser1/sensor/999999996", '{id:999999996,"name":"TestAddSensor","userid":999999998}');
        $this->assertEquals(400, $response->code);
        $response = $this->doHttpPutRequest("UnitTestUser1/sensor/999999996", '{"id:999999996,"name":"TestAddSensor","userid":999999998}');
        $this->assertEquals(400, $response->code);
        $response = $this->doHttpPutRequest("UnitTestUser1/sensor/999999996", '{\'id\':999999996,"name":"TestAddSensor","userid":999999998}');
        $this->assertEquals(400, $response->code);
        $response = $this->doHttpPutRequest("UnitTestUser1/sensor/999999996", '{"name":"TestAddSensor","userid":null}');
        $this->assertEquals(400, $response->code);
    }

    public function testDelete()
    {
        $response = $this->doHttpDeleteRequest("UnitTestUser1/sensor/999999996");
        $this->assertEquals(204, $response->code);

        $response = $this->doHttpGetRequest("UnitTestUser1/sensor");
        $sensors = $response->body;
        $this->assertCount(1, $sensors);
        $this->assertSame(999999997, $sensors[0]->id);
    }

    public function testDeleteWrongUserid()
    {
        $response = $this->doHttpDeleteRequest("UnitTestUser1/sensor/999999998");
        $this->assertEquals(404, $response->code);
    }

    public function testDeleteUnknown()
    {
        $response = $this->doHttpDeleteRequest("UnitTestUser1/sensor/1234567890");
        $this->assertEquals(404, $response->code);
    }

}
