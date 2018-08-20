<?php

use Icawebdesign\Hibp\Paste\Paste;
use Icawebdesign\Hibp\Paste\PasteEntity;
use PHPUnit\Framework\TestCase;

/**
 * Paste tests
 *
 * @author Ian <ian@ianh.io>
 * @since 05/03/2018
 */

class PasteTest extends TestCase
{
    protected $pastes;

    protected const PASTE_LOOKUP = '[{"Source":"Pastebin","Id":"girmPjdy","Title":"roblox","Date":"2018-03-18T04:21:07Z","EmailCount":1171},{"Source":"Pastebin","Id":"62HRhYsQ","Title":null,"Date":"2018-02-14T19:48:39Z","EmailCount":4295},{"Source":"Pastebin","Id":"JUeYgehJ","Title":null,"Date":"2018-02-14T19:46:18Z","EmailCount":4295},{"Source":"Pastebin","Id":"LCKWk6XZ","Title":"ALIBABA phish ** running on wm-aaa[.]org","Date":"2018-01-25T14:33:39Z","EmailCount":122},{"Source":"Pastebin","Id":"dWvNJgHk","Title":"directway","Date":"2018-01-24T16:50:23Z","EmailCount":28},{"Source":"Pastebin","Id":"YrpQA60S","Title":null,"Date":"2018-01-24T07:54:15Z","EmailCount":16476},{"Source":"Pastebin","Id":"G9R3MR3b","Title":null,"Date":"2017-11-17T07:17:38Z","EmailCount":1792},{"Source":"Pastebin","Id":"KuUjKAmQ","Title":"ALIBABA phish ** running on akc.in.ua","Date":"2017-11-03T12:19:24Z","EmailCount":122},{"Source":"Pastebin","Id":"suPshHZ1","Title":null,"Date":"2017-09-06T03:41:33Z","EmailCount":20444},{"Source":"Pastebin","Id":"yv1YiJmq","Title":null,"Date":"2017-08-31T02:28:58Z","EmailCount":7879},{"Source":"Pastebin","Id":"KsXNChr2","Title":"partial of the roblox database","Date":"2017-06-06T06:24:07Z","EmailCount":1171},{"Source":"Pastebin","Id":"Ab2ZYrq4","Title":null,"Date":"2017-05-17T13:37:36Z","EmailCount":48},{"Source":"Pastebin","Id":"46g62dvD","Title":null,"Date":"2017-01-19T13:03:18Z","EmailCount":1670},{"Source":"Pastebin","Id":"R74FwP0k","Title":"Drag#3,7k","Date":"2016-11-19T02:56:58Z","EmailCount":3689},{"Source":"Pastebin","Id":"b9tpGnWe","Title":"\'LeakedSource\' Vulns","Date":"2016-10-07T13:17:45Z","EmailCount":1},{"Source":"Pastebin","Id":"ccBxkCGq","Title":"\'LeakedSource\' Vulns","Date":"2016-10-07T13:02:13Z","EmailCount":1},{"Source":"Pastebin","Id":"MqHprQBz","Title":null,"Date":"2016-09-26T07:58:27Z","EmailCount":11102},{"Source":"Pastebin","Id":"fzNxjs5k","Title":null,"Date":"2016-09-13T02:45:11Z","EmailCount":1},{"Source":"Pastebin","Id":"snhT5XNr","Title":null,"Date":"2016-08-30T17:40:50Z","EmailCount":96},{"Source":"Pastebin","Id":"GDvU8vRL","Title":"DB","Date":"2016-08-13T22:48:34Z","EmailCount":1680},{"Source":"Pastebin","Id":"1F5GkesT","Title":"4k_good","Date":"2016-07-30T12:26:03Z","EmailCount":3801},{"Source":"Pastebin","Id":"aGJikPuq","Title":"xc","Date":"2016-03-01T06:52:29Z","EmailCount":372},{"Source":"Pastebin","Id":"WN4rabci","Title":null,"Date":"2016-01-25T12:01:42Z","EmailCount":5129},{"Source":"Pastebin","Id":"0jspEMnT","Title":null,"Date":"2015-12-08T20:11:46Z","EmailCount":9581},{"Source":"Pastebin","Id":"SRinfjiA","Title":"Toros by wbocdb","Date":"2015-10-25T08:38:31Z","EmailCount":191},{"Source":"Pastebin","Id":"fW7XcwG8","Title":null,"Date":"2015-09-20T14:43:03Z","EmailCount":3873},{"Source":"Pastebin","Id":"xe7afTjQ","Title":null,"Date":"2015-08-14T08:20:26Z","EmailCount":62},{"Source":"Pastebin","Id":"20DBq9VW","Title":null,"Date":"2015-08-14T08:18:52Z","EmailCount":62},{"Source":"Pastebin","Id":"ieaWcJgQ","Title":"leadss","Date":"2015-05-20T14:00:22Z","EmailCount":10191},{"Source":"Pastebin","Id":"8Fy3pWAF","Title":"Leak019","Date":"2015-05-18T00:50:26Z","EmailCount":505},{"Source":"Pastebin","Id":"Y99aRUuP","Title":"UK MAIL LIST FRESH","Date":"2015-05-16T01:50:50Z","EmailCount":19276},{"Source":"Pastebin","Id":"yukVFztc","Title":"hmailserver logs","Date":"2015-02-05T12:38:00Z","EmailCount":33},{"Source":"Pastebin","Id":"tcHtWCFD","Title":"hmailserver logs","Date":"2015-02-05T12:32:00Z","EmailCount":32},{"Source":"Pastebin","Id":"0SqeEgZe","Title":null,"Date":"2015-01-29T09:38:00Z","EmailCount":10001},{"Source":"Pastebin","Id":"h2KJPWJ9","Title":null,"Date":"2014-09-22T14:09:00Z","EmailCount":10127},{"Source":"Pastebin","Id":"L730bR9a","Title":null,"Date":"2014-08-30T00:08:00Z","EmailCount":4073},{"Source":"Pastebin","Id":"ktnvMJDH","Title":null,"Date":"2014-08-29T23:08:00Z","EmailCount":1004},{"Source":"Pastebin","Id":"tJmdW6sp","Title":null,"Date":"2014-08-29T23:08:00Z","EmailCount":10124},{"Source":"Pastebin","Id":"VvKhYPR0","Title":"troyhunt","Date":"2014-08-23T21:08:00Z","EmailCount":113},{"Source":"Pastebin","Id":"B8TeVHVt","Title":"Emails","Date":"2014-08-23T19:08:00Z","EmailCount":7880},{"Source":"QuickLeak","Id":"QtPly6aE","Title":"Islamic Cyber Resistance Hacked blogs.perl.org","Date":"2014-01-22T00:00:00Z","EmailCount":2363},{"Source":"Pastebin","Id":"01ywCrGV","Title":"AnonLeague hack leak email stratfor.com in July-17-2013","Date":"2013-07-18T04:07:00Z","EmailCount":57},{"Source":"AdHocUrl","Id":"http://siph0n.in/exploits.php?id=4560","Title":"BuzzMachines.com 40k+","Date":null,"EmailCount":36959},{"Source":"AdHocUrl","Id":"http://balockae.online/files/BlackMarketReloaded_users.sql","Title":"balockae.online","Date":null,"EmailCount":10547},{"Source":"AdHocUrl","Id":"http://siph0n.in/exploits.php?id=7854","Title":"shop.etchshop.co.uk - [17K] breached by @KittyGods","Date":null,"EmailCount":15113},{"Source":"AdHocUrl","Id":"http://pxahb.xyz/emailpass/www.ironsudoku.com.txt","Title":"pxahb.xyz","Date":null,"EmailCount":64917},{"Source":"AdHocUrl","Id":"http://pxahb.xyz/emailpass/www.optimale-praesentation.de.txt","Title":"pxahb.xyz","Date":null,"EmailCount":190967}]';

    public function setUp()
    {
        parent::setUp();
        $this->pastes = new Paste();
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->pastes = null;
    }

    /** @test */
    public function instance_of_class_should_be_a_Pastes()
    {
        $this->assertInstanceOf(Paste::class, $this->pastes);
    }

    /** @test */
    public function successful_lookup_should_return_a_collection()
    {
        $pasteData = $this->getPasteData();
        $mock = \Mockery::mock(Paste::class);
        $mock->allows()->getStatusCode()->once()->andReturn(200);
        $mock->allows()->lookup()
            ->once()
            ->with('test@example.com')
            ->andReturn($pasteData);

        $data = $mock->lookup('test@example.com');
        
        $this->assertEquals(200, $mock->getStatusCode());
        $this->assertInstanceOf(\Tightenco\Collect\Support\Collection::class, $data);
        $this->assertGreaterThan(0, $data->count());

        $paste = $data->first();

        $this->assertAttributeNotEmpty('source', $paste);
        $this->assertAttributeNotEmpty('id', $paste);
        $this->assertAttributeNotEmpty('date', $paste);
        $this->assertAttributeNotEmpty('emailCount', $paste);
        $this->assertInstanceOf(Carbon\Carbon::class, $paste->getDate());
        $this->assertInternalType('int', $paste->getEmailCount());
        $this->assertGreaterThan(0, $paste->getEmailCount());
    }

    /**
     * @return \Tightenco\Collect\Support\Collection
     */
    protected function getPasteData(): \Tightenco\Collect\Support\Collection
    {
        return \Tightenco\Collect\Support\Collection::make(json_decode(self::PASTE_LOOKUP))
            ->map(function($paste) {
                return new PasteEntity($paste);
            });
    }
}
