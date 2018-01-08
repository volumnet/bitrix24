<?php
namespace VolumNet\Bitrix24;

use PHPUnit_Framework_TestCase;
use Exception;
use VolumNet\CURL\CURL;

/**
 * Quick Lead Tester
 */
class QuickLeadTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests single quotes' invalid JSON fix
     */
    public function testFixJSON()
    {
        $ql = new QuickLead('http://aaa.bbb', 'login', 'password');
        $invalidJson = "{'error':'400','error_message':'Ошибка запроса'}";
        $validJson = $ql->fixJSON($invalidJson);
        $json = json_decode($validJson);
        $this->assertEquals(400, (int)$json->error);

        $invalidJson = '{"error":"400","error_message":"Ошибка за\"проса"}';
        $validJson = $ql->fixJSON($invalidJson);
        $json = json_decode($validJson);
        $this->assertEquals(400, (int)$json->error);
    }

    /**
     * Tests send with no login
     * @expectedException Exception
     * @expectedExceptionMessage No login/password specified
     */
    public function testSendWithNoLogin()
    {
        $ql = new QuickLead('http://aaa.bbb', '', 'password');
        $ql->send(array());
    }


    /**
     * Tests send with no password
     * @expectedException Exception
     * @expectedExceptionMessage No login/password specified
     */
    public function testSendWithNoPassword()
    {
        $ql = new QuickLead('http://aaa.bbb', 'login', '');
        $ql->send(array());
    }


    /**
     * Tests send with no title
     * @expectedException Exception
     * @expectedExceptionMessage No title provided
     */
    public function testSendWithNoTitle()
    {
        $ql = new QuickLead('http://aaa.bbb', 'login', 'password');
        $ql->send(array());
    }


    /**
     * Tests send with no response
     * @expectedException Exception
     * @expectedExceptionMessage No response retrieved
     */
    public function testSendWithNoResponse()
    {
        $ql = new QuickLead('http://aaa.bbb', 'login', 'password');
        $ql->send(array('TITLE' => 'Test lead'));
    }


    /**
     * Tests send with mock URL
     */
    public function testSendMock()
    {
        require_once __DIR__ . '/../mocks/QuickLeadMock.php';
        $ql = new QuickLeadMock('https://httpbin.org/post', 'login', 'password');
        $result = $ql->send(array('TITLE' => 'Test lead'));
        $this->assertEquals('login', $result->form->LOGIN);
        $this->assertEquals('password', $result->form->PASSWORD);
        $this->assertEquals('Test lead', $result->form->TITLE);
    }


    /**
     * Tests send with invalid credentials
     * @expectedException Exception
     * @expectedExceptionCode 403
     * @expectedExceptionMessage Неверный логин или пароль
     */
    public function testSendInvalidCredentials()
    {
        $b24c = $GLOBALS['bitrix24'];
        $ql = new QuickLead($b24c['domain'], 'login', 'password');
        $result = $ql->send(array('TITLE' => 'Test lead'));
    }


    /**
     * Tests real usage
     */
    public function testSend()
    {
        return true; // Comment if necessary to not create new leads uselessly

        $b24c = $GLOBALS['bitrix24'];

        $ql = new QuickLead($b24c['domain'], $b24c['login'], $b24c['password']);
        $data = array(
            'TITLE' => 'Test lead',
            'COMPANY_TITLE' => 'Test company',
            'NAME' => 'User',
            'LAST_NAME' => 'Test',
            'SECOND_NAME' => date('Hi'),
            'ADDRESS' => 'Test address',
            'PHONE_WORK' => '+7 999 000-00-00',
            'EMAIL_WORK' => 'test@test.org',
        );
        $result = $ql->send($data);
        $id = (int)$result->ID;
        $this->assertEquals(QuickLead::ERR_OK, $result->error);
        $this->assertGreaterThan(0, $id);

        $curl = new CURL();
        $url = $b24c['domain'] . '/rest/1/' . $b24c['webhook'] . '/crm.lead.get';
        $result = $curl->getURL($url, array('id' => $id), false, 'jsonObject')->result;
        $this->assertEquals($id, $result->ID);
        $this->assertEquals('Test lead', $result->TITLE);
        $this->assertEquals('+7 999 000-00-00', $result->PHONE[0]->VALUE);
        $this->assertEquals('test@test.org', $result->EMAIL[0]->VALUE);

        $url = $b24c['domain'] . '/rest/1/' . $b24c['webhook'] . '/crm.lead.delete';
        $result = $curl->getURL($url, array('id' => $id), false, 'jsonObject')->result;
        $this->assertEquals(1, $result);

        $url = $b24c['domain'] . '/rest/1/' . $b24c['webhook'] . '/crm.lead.get';
        $result = $curl->getURL($url, array('id' => $id), false, 'jsonObject');
        $this->assertEquals('Not found', $result->error_description);
    }
}
