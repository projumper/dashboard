<?php

namespace Tests\Unit;

use App\Models\Task;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class AddTaskTest extends TestCase
{

    private $jsonString = '{"self":"https://zentralweb.atlassian.net/rest/api/2/26107","id":26107,"key":"TEST-14","changelog":{"startAt":0,"maxResults":0,"total":0,"histories":null},"fields":{"statuscategorychangedate":"2020-11-22T20:07:15.142+0100","issuetype":{"self":"https://zentralweb.atlassian.net/rest/api/2/issuetype/10002","id":10002,"description":"A task that needs to be done.","iconUrl":"https://zentralweb.atlassian.net/secure/viewavatar?size=medium&avatarId=10318&avatarType=issuetype","name":"Task","subtask":false,"fields":null,"statuses":[],"namedValue":"Task"},"timespent":null,"project":{"self":"https://zentralweb.atlassian.net/rest/api/2/project/13166","id":13166,"key":"TEST","name":"TEST","description":null,"avatarUrls":{"48x48":"https://zentralweb.atlassian.net/secure/projectavatar?pid=13166&avatarId=11066","24x24":"https://zentralweb.atlassian.net/secure/projectavatar?size=small&s=small&pid=13166&avatarId=11066","16x16":"https://zentralweb.atlassian.net/secure/projectavatar?size=xsmall&s=xsmall&pid=13166&avatarId=11066","32x32":"https://zentralweb.atlassian.net/secure/projectavatar?size=medium&s=medium&pid=13166&avatarId=11066"},"issuetypes":null,"projectCategory":null,"email":null,"lead":null,"components":null,"versions":null,"projectTypeKey":"software","simplified":false},"fixVersions":[],"customfield_11001":null,"customfield_11002":null,"aggregatetimespent":null,"resolution":null,"customfield_11003":null,"customfield_11004":[],"customfield_11005":null,"customfield_10500":null,"customfield_10700":null,"customfield_10900":null,"resolutiondate":null,"workratio":-1,"watches":{"self":"https://zentralweb.atlassian.net/rest/api/2/issue/TEST-4/watchers","watchCount":0,"isWatching":true},"issuerestriction":{"issuerestrictions":{},"shouldDisplay":false},"lastViewed":null,"created":1606072034951,"customfield_10100":null,"priority":{"self":"https://zentralweb.atlassian.net/rest/api/2/priority/3","id":3,"name":"Medium","iconUrl":"https://zentralweb.atlassian.net/images/icons/priorities/medium.svg","namedValue":"Medium"},"customfield_10300":{"hasEpicLinkFieldDependency":false,"showField":false,"nonEditableReason":{"reason":"PLUGIN_LICENSE_ERROR","message":"Die übergeordnete Verknüpfung ist nur für Jira Premium-Benutzer verfügbar."}},"labels":[],"customfield_10016":null,"customfield_10017":null,"timeestimate":null,"aggregatetimeoriginalestimate":null,"versions":[],"issuelinks":[],"assignee":null,"updated":1606072034951,"status":{"self":"https://zentralweb.atlassian.net/rest/api/2/status/10100","description":"","iconUrl":"https://zentralweb.atlassian.net/","name":"Backlog","untranslatedName":null,"id":10100,"statusCategory":{"self":"https://zentralweb.atlassian.net/rest/api/2/statuscategory/2","id":2,"key":"new","colorName":"blue-gray","name":"New"},"untranslatedNameValue":null},"components":[],"timeoriginalestimate":null,"description":null,"customfield_10210":null,"customfield_10211":null,"customfield_10015":"0|i00kcr:","timetracking":{"originalEstimate":null,"remainingEstimate":null,"timeSpent":null,"originalEstimateSeconds":0,"remainingEstimateSeconds":0,"timeSpentSeconds":0},"customfield_10203":null,"customfield_10204":null,"customfield_10600":null,"security":null,"customfield_10205":null,"customfield_10800":null,"customfield_10206":null,"aggregatetimeestimate":null,"attachment":[],"customfield_10207":null,"customfield_10801":null,"customfield_10802":null,"customfield_10208":null,"customfield_10209":null,"summary":"sada","creator":{"self":"https://zentralweb.atlassian.net/rest/api/2/user?accountId=557058%3Ae33f889f-36f5-476b-a1a7-f21bb2c74915","name":null,"key":null,"accountId":"557058:e33f889f-36f5-476b-a1a7-f21bb2c74915","emailAddress":null,"avatarUrls":{"48x48":"https://secure.gravatar.com/avatar/0f5e53072f0d81e8752af4d24ebff958?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FIG-4.png","24x24":"https://secure.gravatar.com/avatar/0f5e53072f0d81e8752af4d24ebff958?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FIG-4.png","16x16":"https://secure.gravatar.com/avatar/0f5e53072f0d81e8752af4d24ebff958?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FIG-4.png","32x32":"https://secure.gravatar.com/avatar/0f5e53072f0d81e8752af4d24ebff958?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FIG-4.png"},"displayName":"Ivan Gartsev","active":true,"timeZone":"Europe/Berlin","groups":null,"locale":null,"accountType":"atlassian"},"subtasks":[],"customfield_11010":null,"reporter":{"self":"https://zentralweb.atlassian.net/rest/api/2/user?accountId=557058%3Ae33f889f-36f5-476b-a1a7-f21bb2c74915","name":null,"key":null,"accountId":"557058:e33f889f-36f5-476b-a1a7-f21bb2c74915","emailAddress":null,"avatarUrls":{"48x48":"https://secure.gravatar.com/avatar/0f5e53072f0d81e8752af4d24ebff958?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FIG-4.png","24x24":"https://secure.gravatar.com/avatar/0f5e53072f0d81e8752af4d24ebff958?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FIG-4.png","16x16":"https://secure.gravatar.com/avatar/0f5e53072f0d81e8752af4d24ebff958?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FIG-4.png","32x32":"https://secure.gravatar.com/avatar/0f5e53072f0d81e8752af4d24ebff958?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Finitials%2FIG-4.png"},"displayName":"Ivan Gartsev","active":true,"timeZone":"Europe/Berlin","groups":null,"locale":null,"accountType":"atlassian"},"customfield_11011":null,"customfield_10000":null,"aggregateprogress":{"progress":0,"total":0},"customfield_11012":null,"customfield_10001":null,"customfield_10200":null,"customfield_10201":null,"customfield_10202":null,"customfield_10400":"{}","customfield_11006":null,"environment":null,"customfield_11008":null,"customfield_11009":null,"duedate":null,"progress":{"progress":0,"total":0},"votes":{"self":"https://zentralweb.atlassian.net/rest/api/2/issue/TEST-4/votes","votes":0,"hasVoted":false}},"renderedFields":null}';

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testExample()
    {
        //$this->assertTrue(true);
    }

    public function test_add_task()
    {

        $data = json_decode($this->jsonString);
        $data->key = 'TEST-' . rand(1, 15);
        $data = get_object_vars($data);
        //$data = json_encode($data);

        $this->postJson(route('add'), $data)
            ->assertStatus(200)
            ->assertJson(['status' => 'OK']);
    }

    public function test_edit_task()
    {
        //todo random picking task from test table
        //$tasks = Task::all();

        $data = json_decode($this->jsonString);
        $data->key = 'TEST-' . rand(1, 15);
        $data = get_object_vars($data);

        $this->putJson(route('edit'), $data)
            ->assertStatus(200)
            ->assertJson(['status' => 'OK']);
    }
}
