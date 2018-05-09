<?php
/**
 * Breach tests
 *
 * @author Ian <ian@ianh.io>
 * @since 04/03/2018
 */

use Icawebdesign\Hibp\Breach\Breach;
use GuzzleHttp\Exception\GuzzleException;
use Icawebdesign\Hibp\Breach\BreachSiteEntity;
use Icawebdesign\Hibp\Hibp;
use PHPUnit\Framework\TestCase;
use Tightenco\Collect\Support\Collection;

class BreachTest extends TestCase
{
    protected $breaches;

    protected const TOO_MANY_REQUESTS = 429;

    protected const BREACH_SITES = '[{"Title":"000webhost","Name":"000webhost","Domain":"000webhost.com","BreachDate":"2015-03-01","AddedDate":"2015-10-26T23:35:45Z","ModifiedDate":"2017-12-10T21:44:27Z","PwnCount":14936670,"Description":"In approximately March 2015, the free web hosting provider <a href=\"http://www.troyhunt.com/2015/10/breaches-traders-plain-text-passwords.html\" target=\"_blank\" rel=\"noopener\">000webhost suffered a major data breach</a> that exposed almost 15 million customer records. The data was sold and traded before 000webhost was alerted in October. The breach included names, email addresses and plain text passwords.","DataClasses":["Email addresses","IP addresses","Names","Passwords"],"IsVerified":true,"IsFabricated":false,"IsSensitive":false,"IsActive":true,"IsRetired":false,"IsSpamList":false,"LogoType":"png"},{"Title":"126","Name":"126","Domain":"126.com","BreachDate":"2012-01-01","AddedDate":"2016-10-08T07:46:05Z","ModifiedDate":"2016-10-08T07:46:05Z","PwnCount":6414191,"Description":"In approximately 2012, it\'s alleged that the Chinese email service known as <a href=\"http://126.com\" target=\"_blank\" rel=\"noopener\">126</a> suffered a data breach that impacted 6.4 million subscribers. Whilst there is evidence that the data is legitimate, due to the difficulty of emphatically verifying the Chinese breach it has been flagged as &quot;unverified&quot;. The data in the breach contains email addresses and plain text passwords. <a href=\"https://www.troyhunt.com/handling-chinese-data-breaches-in-have-i-been-pwned/\" target=\"_blank\" rel=\"noopener\">Read more about Chinese data breaches in Have I been pwned.</a>","DataClasses":["Email addresses","Passwords"],"IsVerified":false,"IsFabricated":false,"IsSensitive":false,"IsActive":true,"IsRetired":false,"IsSpamList":false,"LogoType":"svg"}]';

    protected const DATA_CLASSES = '["Account balances","Address book contacts","Age groups","Ages","Apps installed on devices","Astrological signs","Auth tokens","Avatars","Bank account numbers","Banking PINs","Beauty ratings","Biometric data","Browser user agent details","Buying preferences","Car ownership statuses","Career levels","Cellular network names","Charitable donations","Chat logs","Credit card CVV","Credit cards","Credit status information","Customer feedback","Customer interactions","Dates of birth","Deceased date","Deceased statuses","Device information","Device usage tracking data","Drinking habits","Drug habits","Eating habits","Education levels","Email addresses","Email messages","Employers","Ethnicities","Family members\' names","Family plans","Family structure","Financial investments","Financial transactions","Fitness levels","Genders","Geographic locations","Government issued IDs","Health insurance information","Historical passwords","Home ownership statuses","Homepage URLs","IMEI numbers","IMSI numbers","Income levels","Instant messenger identities","IP addresses","Job titles","MAC addresses","Marital statuses","Names","Nationalities","Net worths","Nicknames","Occupations","Parenting plans","Partial credit card data","Passport numbers","Password hints","Passwords","Payment histories","Payment methods","Personal descriptions","Personal health data","Personal interests","Phone numbers","Physical addresses","Physical attributes","Political donations","Political views","Private messages","Professional skills","Profile photos","Purchases","Purchasing habits","Races","Recovery email addresses","Relationship statuses","Religions","Reward program balances","Salutations","School grades (class levels)","Security questions and answers","Sexual fetishes","Sexual orientations","Smoking habits","SMS messages","Social connections","Social media profiles","Spoken languages","Support tickets","Survey results","Time zones","Travel habits","User statuses","User website URLs","Usernames","Utility bills","Vehicle details","Website activity","Work habits","Years of birth","Years of professional experience"]';

    protected const BREACHED_ACCOUNT = '[{"Title":"000webhost","Name":"000webhost","Domain":"000webhost.com","BreachDate":"2015-03-01","AddedDate":"2015-10-26T23:35:45Z","ModifiedDate":"2017-12-10T21:44:27Z","PwnCount":14936670,"Description":"In approximately March 2015, the free web hosting provider <a href=\"http://www.troyhunt.com/2015/10/breaches-traders-plain-text-passwords.html\" target=\"_blank\" rel=\"noopener\">000webhost suffered a major data breach</a> that exposed almost 15 million customer records. The data was sold and traded before 000webhost was alerted in October. The breach included names, email addresses and plain text passwords.","DataClasses":["Email addresses","IP addresses","Names","Passwords"],"IsVerified":true,"IsFabricated":false,"IsSensitive":false,"IsActive":true,"IsRetired":false,"IsSpamList":false,"LogoType":"png"},{"Title":"8tracks","Name":"8tracks","Domain":"8tracks.com","BreachDate":"2017-06-27","AddedDate":"2018-02-16T07:09:30Z","ModifiedDate":"2018-02-16T07:09:30Z","PwnCount":7990619,"Description":"In June 2017, the online playlists service known as <a href=\"https://blog.8tracks.com/2017/06/27/password-security-alert/\" target=\"_blank\" rel=\"noopener\">8Tracks suffered a data breach</a> which impacted 18 million accounts. In their disclosure, 8Tracks advised that &quot;the vector for the attack was an employee’s GitHub account, which was not secured using two-factor authentication&quot;. Salted SHA-1 password hashes for users who <em>didn\'t</em> sign up with either Google or Facebook authentication were also included. The data was provided to HIBP by whitehat security researcher and data analyst Adam Davies and contained almost 8 million unique email addresses.","DataClasses":["Email addresses","Passwords"],"IsVerified":true,"IsFabricated":false,"IsSensitive":false,"IsActive":true,"IsRetired":false,"IsSpamList":false,"LogoType":"png"},{"Title":"Adobe","Name":"Adobe","Domain":"adobe.com","BreachDate":"2013-10-04","AddedDate":"2013-12-04T00:00:00Z","ModifiedDate":"2013-12-04T00:00:00Z","PwnCount":152445165,"Description":"In October 2013, 153 million Adobe accounts were breached with each containing an internal ID, username, email, <em>encrypted</em> password and a password hint in plain text. The password cryptography was poorly done and <a href=\"http://stricture-group.com/files/adobe-top100.txt\" target=\"_blank\" rel=\"noopener\">many were quickly resolved back to plain text</a>. The unencrypted hints also <a href=\"http://www.troyhunt.com/2013/11/adobe-credentials-and-serious.html\" target=\"_blank\" rel=\"noopener\">disclosed much about the passwords</a> adding further to the risk that hundreds of millions of Adobe customers already faced.","DataClasses":["Email addresses","Password hints","Passwords","Usernames"],"IsVerified":true,"IsFabricated":false,"IsSensitive":false,"IsActive":true,"IsRetired":false,"IsSpamList":false,"LogoType":"svg"},{"Title":"Bitcoin Talk","Name":"BitcoinTalk","Domain":"bitcointalk.org","BreachDate":"2015-05-22","AddedDate":"2017-03-27T23:45:41Z","ModifiedDate":"2017-03-27T23:45:41Z","PwnCount":501407,"Description":"In May 2015, the Bitcoin forum <a href=\"https://www.cryptocoinsnews.com/bitcoin-exchange-btc-e-bitcointalk-forum-breaches-details-revealed/\" target=\"_blank\" rel=\"noopener\">Bitcoin Talk was hacked</a> and over 500k unique email addresses were exposed. The attack led to the exposure of a raft of personal data including usernames, email and IP addresses, genders, birth dates, security questions and MD5 hashes of their answers plus hashes of the passwords themselves.","DataClasses":["Dates of birth","Email addresses","Genders","IP addresses","Passwords","Security questions and answers","Usernames","Website activity"],"IsVerified":true,"IsFabricated":false,"IsSensitive":false,"IsActive":true,"IsRetired":false,"IsSpamList":false,"LogoType":"svg"}]';

    /**
     * Add delay between tests to prevent hitting rate limit
     *
     * @see https://haveibeenpwned.com/API/v2#RateLimiting
     *
     * @param float $delay
     */
    protected function addDelay(float $delay = 1.2)
    {
        usleep($delay * 1000000);
    }

    public function setUp()
    {
        parent::setUp();
        $config = Hibp::loadConfig();

        $this->breaches = new Breach($config);
    }

    public function tearDown()
    {
        $this->breaches = null;
    }

    /** @test */
    public function instance_of_class_should_be_a_Breaches()
    {
        $this->assertInstanceOf(Breach::class, $this->breaches);
    }

    /** @test */
    public function getting_all_breachesites_should_return_a_collection()
    {
        $data = $this->getBreachSitesData();

        $mock = Mockery::mock();
        $mock->shouldReceive('getStatusCode')->once()->andReturn(200);
        $mock->shouldReceive('getAllBreachSites')->once()->andReturn($data);
        $mock->shouldReceive('count')->once()->andReturn($data->count());
        $mock->shouldReceive('first')->once()->andReturn($data->first());

        $breaches = $mock->getAllBreachSites();

        $this->assertEquals(200, $mock->getStatusCode());
        $this->assertInstanceOf(Collection::class, $breaches);
        $this->assertGreaterThan(0, $mock->count());
        $this->assertInstanceOf(BreachSiteEntity::class, $mock->first());
    }

    /** @test */
    public function successful_breach_lookup_should_return_BreachSiteEntity()
    {
        $data = $this->getBreachSitesData();

        $mock = \Mockery::mock();
        $mock->shouldReceive('getStatusCode')->once()->andReturn(200);
        $mock->shouldReceive('getBreach')
             ->once()->with('000webhost')
                     ->andReturn($data->first());

        $breach = $mock->getBreach('000webhost');

        $this->assertInstanceOf(BreachSiteEntity::class, $breach);
        $this->assertAttributeNotEmpty('title', $breach);
        $this->assertAttributeNotEmpty('name', $breach);
        $this->assertAttributeNotEmpty('domain', $breach);
        $this->assertAttributeNotEmpty('breachDate', $breach);
        $this->assertAttributeNotEmpty('addedDate', $breach);
        $this->assertAttributeNotEmpty('modifiedDate', $breach);
        $this->assertAttributeNotEmpty('pwnCount', $breach);
        $this->assertAttributeNotEmpty('description', $breach);
        $this->assertAttributeNotEmpty('dataClasses', $breach);
        $this->assertAttributeInternalType('bool', 'verified', $breach);
        $this->assertAttributeInternalType('bool', 'fabricated', $breach);
        $this->assertAttributeInternalType('bool', 'sensitive', $breach);
        $this->assertAttributeInternalType('bool', 'active', $breach);
        $this->assertAttributeInternalType('bool', 'retired', $breach);
        $this->assertAttributeInternalType('bool', 'spamList', $breach);
        $this->assertAttributeNotEmpty('logoType', $breach);
    }

    /** @test */
    public function getting_all_dataclasses_should_return_a_collection()
    {
        $data = $this->getDataClassesData();

        $mock = \Mockery::mock();
        $mock->shouldReceive('getStatusCode')->once()->andReturn(200);
        $mock->shouldReceive('getAllDataClasses')->once()->andReturn($data);

        $dataClasses = $mock->getAllDataClasses();
        $this->assertEquals(200, $mock->getStatusCode());
        $this->assertInstanceOf(Collection::class, $dataClasses);
    }

    /** @test */
    public function getting_breach_data_for_account_should_return_a_collection()
    {
        $data = $this->getBreachedAccountData();
        $mock = \Mockery::mock();
        $mock->shouldReceive('getStatusCode')->once()->andReturn(200);
        $mock->shouldReceive('getBreachedAccount')
             ->once()->with('test@example.com')
             ->andReturn($data);

        $breaches = $mock->getBreachedAccount('test@example.com');

        $this->assertEquals(200, $mock->getStatusCode());
        $this->assertInstanceOf(Collection::class, $breaches);
        $this->assertGreaterThan(0, $breaches->count());
        $this->assertInstanceOf(BreachSiteEntity::class, $breaches->first());
    }

    /**
     * @return Collection
     */
    protected function getBreachSitesData(): Collection
    {
        return collect(json_decode(self::BREACH_SITES))
            ->map(function($breach) {
                return new BreachSiteEntity($breach);
            });
    }

    /**
     * @return Collection
     */
    protected function getDataClassesData(): Collection
    {
        return collect(json_decode(self::DATA_CLASSES));
    }

    /**
     * @return Collection
     */
    protected function getBreachedAccountData(): Collection
    {
        return collect(json_decode(self::BREACHED_ACCOUNT))
            ->map(function($breach) {
                return new BreachSiteEntity($breach);
            });
    }
}
