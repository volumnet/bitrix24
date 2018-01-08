<?php
namespace VolumNet\Bitrix24;

use PHPUnit_Framework_TestCase;
use Exception;

/**
 * Webhook Tester
 */
class WebhookTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests with no login
     * @expectedException Exception
     * @expectedExceptionMessage No response retrieved
     */
    public function testMethodInvalidDomain()
    {
        $wh = new Webhook('http://aaa.bbb', 'abc');
        $wh->method('someMethod', array());
    }


    /**
     * Tests with invalid JSON
     * @expectedException Exception
     * @expectedExceptionMessage Cannot parse JSON
     */
    public function testMethodInvalidJson()
    {
        $wh = new Webhook('http://httpbin.org', 'abc');
        $wh->method('someMethod', array());
    }


    /**
     * Tests with invalid webhook ID
     * @expectedException Exception
     * @expectedExceptionMessage INVALID_CREDENTIALS
     */
    public function testMethodInvalidWebhook()
    {
        $b24c = $GLOBALS['bitrix24'];
        $wh = new Webhook($b24c['domain'], 'abc');
        $wh->method('crm.lead.get', array());
    }


    /**
     * Tests with invalid method
     * @expectedException Exception
     * @expectedExceptionMessage ERROR_METHOD_NOT_FOUND
     */
    public function testMethodInvalidMethod()
    {
        $b24c = $GLOBALS['bitrix24'];
        $wh = new Webhook($b24c['domain'], 'abc');
        $wh->method('someMethod', array());
    }


    /**
     * Test valid webhook
     */
    public function testMethod()
    {
        $b24c = $GLOBALS['bitrix24'];
        $wh = new Webhook($b24c['domain'], $b24c['webhook']);
        $result = $wh->method('profile', array());
        $this->assertGreaterThan(0, (int)$result->result->ID);
    }


    /**
     * Test of lead creation
     */
    public function testMethodCreateLead()
    {
        $b24c = $GLOBALS['bitrix24'];
        $wh = new Webhook($b24c['domain'], $b24c['webhook']);
        $data = array(
            'fields' => array(
                'TITLE' => 'Test lead',
                'COMPANY_TITLE' => 'Test company',
                'NAME' => 'User',
                'LAST_NAME' => 'Test',
                'SECOND_NAME' => date('Hi'),
                'ADDRESS' => 'Test address',
                'SOURCE_ID' => 'WEB',
                'PHONE' => array(
                    array(
                        'VALUE' => '+7 999 000-00-00',
                        'VALUE_TYPE' => 'WORK'
                    )
                ),
                'EMAIL' => array(
                    array(
                        'VALUE' => 'test@test.org',
                        'VALUE_TYPE' => 'WORK'
                    )
                )
            )
        );
        $result = $wh->method('crm.lead.add', $data);
        $id = (int)$result->result;
        $this->assertGreaterThan(0, $id);

        $result = $wh->method('crm.lead.get', array('id' => $id))->result;
        $this->assertEquals($id, $result->ID);
        $this->assertEquals('Test lead', $result->TITLE);
        $this->assertEquals('+7 999 000-00-00', $result->PHONE[0]->VALUE);
        $this->assertEquals('test@test.org', $result->EMAIL[0]->VALUE);

        $result = $wh->method('crm.lead.delete', array('id' => $id));
        $this->assertEquals(1, $result->result);

        $result = $wh->method('crm.lead.get', array('id' => $id), false, 'jsonObject');
        $this->assertEquals('Not found', $result->error_description);
    }
}
