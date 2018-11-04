<?php
require_once __DIR__ . "/helper/RestApiTestCase.class.php";

class SensorDataTest extends RestApiTestCase
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
        $response = $this->doHttpGetRequest("UnitTestUser1/sensordata");
        $this->assertEquals(200, $response->code);
        $sensordata = $response->body;
        $this->assertCount(6, $sensordata);
        $this->assertSame(999999988, $sensordata[0]->id);
        $this->assertSame(1, $sensordata[0]->data);
        $this->assertSame("2018-10-03 01:00:00", $sensordata[0]->timestamp);
        $this->assertSame(999999996, $sensordata[0]->sensorid);
        $this->assertSame(999999989, $sensordata[1]->id);
        $this->assertSame(2, $sensordata[1]->data);
        $this->assertSame("2018-10-03 02:00:00", $sensordata[1]->timestamp);
        $this->assertSame(999999996, $sensordata[1]->sensorid);
        $this->assertSame(999999990, $sensordata[2]->id);
        $this->assertSame(3, $sensordata[2]->data);
        $this->assertSame("2018-10-03 03:00:00", $sensordata[2]->timestamp);
        $this->assertSame(999999996, $sensordata[2]->sensorid);
        $this->assertSame(999999991, $sensordata[3]->id);
        $this->assertSame(4, $sensordata[3]->data);
        $this->assertSame("2018-10-03 04:00:00", $sensordata[3]->timestamp);
        $this->assertSame(999999997, $sensordata[3]->sensorid);
        $this->assertSame(999999992, $sensordata[4]->id);
        $this->assertSame(5, $sensordata[4]->data);
        $this->assertSame("2018-10-03 05:00:00", $sensordata[4]->timestamp);
        $this->assertSame(999999997, $sensordata[4]->sensorid);
        $this->assertSame(999999993, $sensordata[5]->id);
        $this->assertSame(6, $sensordata[5]->data);
        $this->assertSame("2018-10-03 06:00:00", $sensordata[5]->timestamp);
        $this->assertSame(999999997, $sensordata[5]->sensorid);
    }

    public function testGetById()
    {
        $response = $this->doHttpGetRequest("UnitTestUser1/sensordata/999999988");
        $this->assertEquals(200, $response->code);
        $sensordata = $response->body;
        $this->assertSame(999999988, $sensordata->id);
        $this->assertSame(1, $sensordata->data);
        $this->assertSame("2018-10-03 01:00:00", $sensordata->timestamp);
        $this->assertSame(999999996, $sensordata->sensorid);
    }

    public function testGetByIdOfSensorOfOtherUser()
    {
        $response = $this->doHttpGetRequest("UnitTestUser1/sensordata/999999994");
        $this->assertEquals(404, $response->code);
        $this->assertNotContains("{", $response->body);
        $this->assertNotContains("2018-10-03 07:00:00", $response->body);
    }

    public function testGetByIdUnknown()
    {
        $response = $this->doHttpGetRequest("UnitTestUser1/sensordata/1234567890");
        $this->assertEquals(404, $response->code);
    }

    public function testGetBySensor()
    {
        $response = $this->doHttpGetRequest("UnitTestUser1/sensordata/sensor/999999996");
        $this->assertEquals(200, $response->code);
        $sensordata = $response->body;
        $this->assertCount(3, $sensordata);
        $this->assertSame(999999988, $sensordata[0]->id);
        $this->assertSame(1, $sensordata[0]->data);
        $this->assertSame("2018-10-03 01:00:00", $sensordata[0]->timestamp);
        $this->assertSame(999999996, $sensordata[0]->sensorid);
        $this->assertSame(999999989, $sensordata[1]->id);
        $this->assertSame(2, $sensordata[1]->data);
        $this->assertSame("2018-10-03 02:00:00", $sensordata[1]->timestamp);
        $this->assertSame(999999996, $sensordata[1]->sensorid);
        $this->assertSame(999999990, $sensordata[2]->id);
        $this->assertSame(3, $sensordata[2]->data);
        $this->assertSame("2018-10-03 03:00:00", $sensordata[2]->timestamp);
        $this->assertSame(999999996, $sensordata[2]->sensorid);
    }

    public function testGetBySensorOfOtherUser()
    {
        $response = $this->doHttpGetRequest("UnitTestUser1/sensordata/sensor/999999998");
        $this->assertEquals(404, $response->code);
    }

    public function testGetBySensorWithUnknownSensor()
    {
        $response = $this->doHttpGetRequest("UnitTestUser1/sensordata/sensor/999999998");
        $this->assertEquals(404, $response->code);
    }

    public function testAdd()
    {
        $response = $this->doHttpPostRequest("UnitTestUser1/sensordata", '{"id":null,"data":9444,"timestamp":null,"sensorid":999999996}');
        $this->assertEquals(204, $response->code);

        $requestPath = substr($response->headers['Location'], strlen(self::$baseurl));
        $id = (int)substr($response->headers['Location'], strrpos($response->headers['Location'], "/") + 1);

        $response = $this->doHttpGetRequest($requestPath);
        $sensordata = $response->body;
        $this->assertSame($id, $sensordata->id);
        $this->assertSame(9444, $sensordata->data);
        $this->assertSame(999999996, $sensordata->sensorid);
    }

    public function testAddWithWrongSensorId()
    {
        $response = $this->doHttpPostRequest("UnitTestUser1/sensordata", '{"id":null,"data":9444,"timestamp":null,"sensorid":999999998}');
        $this->assertEquals(404, $response->code);
    }

    public function testAddInvalidJson()
    {
        $response = $this->doHttpPostRequest("UnitTestUser1/sensordata", '{id:null,"data":9444,"timestamp":null,"sensorid":999999996}');
        $this->assertEquals(400, $response->code);
        $response = $this->doHttpPostRequest("UnitTestUser1/sensordata", '{"id:null,"data":9444,"timestamp":null,"sensorid":999999996}');
        $this->assertEquals(400, $response->code);
        $response = $this->doHttpPostRequest("UnitTestUser1/sensordata", '{\'id\':null,"data":9444,"timestamp":null,"sensorid":999999996}');
        $this->assertEquals(400, $response->code);
        $response = $this->doHttpPostRequest("UnitTestUser1/sensordata", '{"id":null,"data":9444,"sensorid":999999998}');
        $this->assertEquals(400, $response->code);
    }

    public function testEdit()
    {
        $response = $this->doHttpPutRequest("UnitTestUser1/sensordata/999999988", '{"id":999999988,"data":9444,"timestamp":null,"sensorid":999999996}');
        $this->assertEquals(404, $response->code);
    }

    public function testDelete()
    {
        $response = $this->doHttpDeleteRequest("UnitTestUser1/sensordata/999999988");
        $this->assertEquals(204, $response->code);

        $response = $this->doHttpGetRequest("UnitTestUser1/sensordata");
        $sensordata = $response->body;
        $this->assertCount(5, $sensordata);
        $this->assertSame(999999989, $sensordata[0]->id);
    }

    public function testDeleteWrongUserid()
    {
        $response = $this->doHttpDeleteRequest("UnitTestUser1/sensordata/999999994");
        $this->assertEquals(404, $response->code);
    }

    public function testDeleteUnknown()
    {
        $response = $this->doHttpDeleteRequest("UnitTestUser1/sensordata/1234567890");
        $this->assertEquals(404, $response->code);
    }

}
