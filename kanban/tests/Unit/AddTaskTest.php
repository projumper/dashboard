<?php

namespace Tests\Unit;

use App\Models\Task;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;


class AddTaskTest extends TestCase
{

    //use DatabaseMigrations;

    private $jsonString = '{"self":"https://zentralweb.atlassian.net/rest/api/2/26195","id":26195,"key":"ZW-235","changelog":{"startAt":0,"maxResults":0,"total":0,"histories":null},"fields":{"statuscategorychangedate":"2020-12-21T07:22:57.492+0100","issuetype":{"self":"https://zentralweb.atlassian.net/rest/api/2/issuetype/10002","id":10002,"description":"A task that needs to be done.","iconUrl":"https://zentralweb.atlassian.net/secure/viewavatar?size=medium&avatarId=10318&avatarType=issuetype","name":"Task","subtask":false,"fields":null,"statuses":[],"namedValue":"Task"},"timespent":18000,"project":{"self":"https://zentralweb.atlassian.net/rest/api/2/project/12300","id":12300,"key":"ZW","name":"ZentralWeb","description":null,"avatarUrls":{"48x48":"https://zentralweb.atlassian.net/secure/projectavatar?pid=12300&avatarId=11077","24x24":"https://zentralweb.atlassian.net/secure/projectavatar?size=small&s=small&pid=12300&avatarId=11077","16x16":"https://zentralweb.atlassian.net/secure/projectavatar?size=xsmall&s=xsmall&pid=12300&avatarId=11077","32x32":"https://zentralweb.atlassian.net/secure/projectavatar?size=medium&s=medium&pid=12300&avatarId=11077"},"issuetypes":null,"projectCategory":null,"email":null,"lead":null,"components":null,"versions":null,"projectTypeKey":"software","simplified":false},"customfield_11001":null,"fixVersions":[],"aggregatetimespent":18000,"customfield_11002":null,"customfield_11003":null,"resolution":null,"customfield_11004":[],"customfield_11005":null,"customfield_10500":null,"customfield_10700":null,"customfield_10900":null,"resolutiondate":null,"workratio":125,"lastViewed":"2020-12-23T11:36:36.544+0100","issuerestriction":{"issuerestrictions":{},"shouldDisplay":false},"watches":{"self":"https://zentralweb.atlassian.net/rest/api/2/issue/ZW-235/watchers","watchCount":1,"isWatching":false},"created":1608531777086,"customfield_10100":null,"priority":{"self":"https://zentralweb.atlassian.net/rest/api/2/priority/3","id":3,"name":"Medium","iconUrl":"https://zentralweb.atlassian.net/images/icons/priorities/medium.svg","namedValue":"Medium"},"customfield_10300":{"hasEpicLinkFieldDependency":false,"showField":false,"nonEditableReason":{"reason":"PLUGIN_LICENSE_ERROR","message":"Die übergeordnete Verknüpfung ist nur für Jira Premium-Benutzer verfügbar."}},"labels":[],"customfield_10016":null,"customfield_10017":null,"timeestimate":0,"aggregatetimeoriginalestimate":14400,"versions":[],"issuelinks":[],"assignee":{"self":"https://zentralweb.atlassian.net/rest/api/2/user?accountId=557058%3A660975c1-9644-4563-bcce-6b0b638207ef","name":null,"key":null,"accountId":"557058:660975c1-9644-4563-bcce-6b0b638207ef","emailAddress":null,"avatarUrls":{"48x48":"https://secure.gravatar.com/avatar/ca6924ab6040ff5c6524feed1e38ebdb?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FIR-1.png","24x24":"https://secure.gravatar.com/avatar/ca6924ab6040ff5c6524feed1e38ebdb?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FIR-1.png","16x16":"https://secure.gravatar.com/avatar/ca6924ab6040ff5c6524feed1e38ebdb?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FIR-1.png","32x32":"https://secure.gravatar.com/avatar/ca6924ab6040ff5c6524feed1e38ebdb?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FIR-1.png"},"displayName":"Ivan Russkykh","active":true,"timeZone":"Europe/Moscow","groups":null,"locale":null,"accountType":"atlassian"},"updated":1608719813272,"status":{"self":"https://zentralweb.atlassian.net/rest/api/2/status/10101","description":"","iconUrl":"https://zentralweb.atlassian.net/","name":"Selected for Development","untranslatedName":null,"id":10101,"statusCategory":{"self":"https://zentralweb.atlassian.net/rest/api/2/statuscategory/2","id":2,"key":"new","colorName":"blue-gray","name":"New"},"untranslatedNameValue":null},"components":[],"timeoriginalestimate":14400,"description":"Prepare files & instructions for install Magento Online Designer module documentation","customfield_10210":null,"customfield_10211":null,"timetracking":{"originalEstimate":"4h","remainingEstimate":"0h","timeSpent":"5h","originalEstimateSeconds":14400,"remainingEstimateSeconds":0,"timeSpentSeconds":18000},"customfield_10015":"0|i00kvv:","customfield_10203":null,"customfield_10600":null,"customfield_10204":null,"security":null,"customfield_10205":null,"customfield_10206":null,"customfield_10800":null,"attachment":[{"self":"https://zentralweb.atlassian.net/rest/api/2/attachment/20848","id":20848,"filename":"online-designer.zip","author":{"self":"https://zentralweb.atlassian.net/rest/api/2/user?accountId=557058%3A660975c1-9644-4563-bcce-6b0b638207ef","name":null,"key":null,"accountId":"557058:660975c1-9644-4563-bcce-6b0b638207ef","emailAddress":null,"avatarUrls":{"48x48":"https://secure.gravatar.com/avatar/ca6924ab6040ff5c6524feed1e38ebdb?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FIR-1.png","24x24":"https://secure.gravatar.com/avatar/ca6924ab6040ff5c6524feed1e38ebdb?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FIR-1.png","16x16":"https://secure.gravatar.com/avatar/ca6924ab6040ff5c6524feed1e38ebdb?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FIR-1.png","32x32":"https://secure.gravatar.com/avatar/ca6924ab6040ff5c6524feed1e38ebdb?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FIR-1.png"},"displayName":"Ivan Russkykh","active":true,"timeZone":"Europe/Moscow","groups":null,"locale":null,"accountType":"atlassian"},"created":1608531839635,"size":1942797,"mimeType":"application/zip","content":"https://zentralweb.atlassian.net/secure/attachment/20848/online-designer.zip"}],"aggregatetimeestimate":0,"customfield_10207":null,"customfield_10801":null,"customfield_10802":null,"customfield_10208":null,"customfield_10209":null,"summary":"Magento Online Designer module documentation","creator":{"self":"https://zentralweb.atlassian.net/rest/api/2/user?accountId=557058%3A660975c1-9644-4563-bcce-6b0b638207ef","name":null,"key":null,"accountId":"557058:660975c1-9644-4563-bcce-6b0b638207ef","emailAddress":null,"avatarUrls":{"48x48":"https://secure.gravatar.com/avatar/ca6924ab6040ff5c6524feed1e38ebdb?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FIR-1.png","24x24":"https://secure.gravatar.com/avatar/ca6924ab6040ff5c6524feed1e38ebdb?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FIR-1.png","16x16":"https://secure.gravatar.com/avatar/ca6924ab6040ff5c6524feed1e38ebdb?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FIR-1.png","32x32":"https://secure.gravatar.com/avatar/ca6924ab6040ff5c6524feed1e38ebdb?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FIR-1.png"},"displayName":"Ivan Russkykh","active":true,"timeZone":"Europe/Moscow","groups":null,"locale":null,"accountType":"atlassian"},"subtasks":[],"customfield_11010":"2020-12-23","reporter":{"self":"https://zentralweb.atlassian.net/rest/api/2/user?accountId=557058%3A660975c1-9644-4563-bcce-6b0b638207ef","name":null,"key":null,"accountId":"557058:660975c1-9644-4563-bcce-6b0b638207ef","emailAddress":null,"avatarUrls":{"48x48":"https://secure.gravatar.com/avatar/ca6924ab6040ff5c6524feed1e38ebdb?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FIR-1.png","24x24":"https://secure.gravatar.com/avatar/ca6924ab6040ff5c6524feed1e38ebdb?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FIR-1.png","16x16":"https://secure.gravatar.com/avatar/ca6924ab6040ff5c6524feed1e38ebdb?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FIR-1.png","32x32":"https://secure.gravatar.com/avatar/ca6924ab6040ff5c6524feed1e38ebdb?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FIR-1.png"},"displayName":"Ivan Russkykh","active":true,"timeZone":"Europe/Moscow","groups":null,"locale":null,"accountType":"atlassian"},"customfield_11011":null,"aggregateprogress":{"progress":18000,"total":18000,"percent":100},"customfield_11012":null,"customfield_10000":null,"customfield_10001":null,"customfield_10200":null,"customfield_10201":null,"customfield_10202":null,"customfield_10400":"{}","customfield_11006":null,"customfield_11008":null,"environment":null,"customfield_11009":null,"duedate":"2020-12-31","progress":{"progress":18000,"total":18000,"percent":100},"votes":{"self":"https://zentralweb.atlassian.net/rest/api/2/issue/ZW-235/votes","votes":0,"hasVoted":false}},"renderedFields":null}';


    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testBasic()
    {
        /*
        $this->assertTrue(true);

        $response = $this->get('/');

        $response->dumpHeaders();

        $response->dumpSession();

        $response->dump();
 */
    }

    public function test_get_worklog()
    {
        //$this->getJson('api/v1/getworklog/key/ZW-230')
        //    ->assertStatus(200);
    }


    /**
     * @dataProvider keyProvider
     */
    public function test_add_task($p_id_nr)
    {
        $taskData = $this->getJson('api/v1/gettaskdata/key/' . $p_id_nr);
        $taskData1 = $taskData->getContent();
        $jsonstring = json_decode($taskData1);
        $data = get_object_vars($jsonstring);
        //dd($jsonstring->key);
        if (isset($jsonstring->key) && $jsonstring->key == $p_id_nr) {

            $this->postJson(route('add'), $data)
                ->assertStatus(200)
                ->assertJson(['status' => 'OK']);
        }
    }

    /**
     * @dataProvider keyProvider
     */
    public function test_edit_task($p_id_nr)
    {
        $taskData = $this->getJson('api/v1/gettaskdata/key/' . $p_id_nr);
        $taskData1 = $taskData->getContent();
        $jsonstring = json_decode($taskData1);
        $data = get_object_vars($jsonstring);

        //dd($data);

        if (isset($jsonstring->key) && $jsonstring->key == $p_id_nr) {
            $this->putJson(route('edit'), $data)
                ->assertStatus(200)
                ->assertJson(['status' => 'OK']);
        }
    }

    public function keyProvider()
    {
        $keyArray = array();

        $projectArray = array('ERICA30','DSET', 'HKDSHOP', 'BS', 'HKD', 'ZW', 'TIM', 'IM20', 'BL', 'ADC', 'SVB', 'EL', 'ET', 'GOT', 'GF', 'HSSEO', 'HCED', 'HS', 'IZ', 'KAIM', 'TAXI', 'KDS', 'WAS');

        foreach ($projectArray as $project) {
            for ($i = 1; $i <= 350; $i++) {

                $p_id_nr = $project . '-' . $i;

                $keyArray[] = ['key' => $p_id_nr];
            }
        }

        return $keyArray;

    }
}
