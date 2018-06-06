<?php
/**
 * PwnedPassword tests
 *
 * @author Ian <ian@ianh.io>
 * @since 28/02/2018
 */

use GuzzleHttp\Exception\GuzzleException;
use Icawebdesign\Hibp\Hibp;
use Icawebdesign\Hibp\Password\PwnedPassword;
use PHPUnit\Framework\TestCase;
use Tightenco\Collect\Support\Collection;

class PwnedPasswordTest extends TestCase
{
    protected $pwnedPassword;

    protected const RANGE_DATA = '280F4AE3BB6C3FB8AE9349C6D246A27E941:3
28108ED7C1CD365BA11E5999D73AC2689BE:3
286E3B507EF6401E6E309B455287A032329:4
2965BF6224D6F4F02E116EF29AF2371B53E:1
2996B3460A77F5EF04B85828D4C23C8439E:1
29B59205F57C2608CAE47E8157621FF7645:1
2B766777053A89201D8257221BBC161E279:2
2D10A6654B6D75908AE572559542245CBFA:2
2D6980B9098804E7A83DC5831BFBAF3927F:1
2D8D1B3FAACCA6A3C6A91617B2FA32E2F57:1
2DC183F740EE76F27B78EB39C8AD972A757:47205
2DE4C0087846D223DBBCCF071614590F300:2
2DEA2B1D02714099E4B7A874B4364D518F6:1
2E90B7B3C5C1181D16C48E273D9AC7F3C16:1
2EAE5EA981BFAF29A8869A40BDDADF3879B:1
2F1AC09E3846595E436BBDDDD2189358AF9:1
2F5296F1244E7CCF7017629E98E24DD8A21:1
30151E8272224C96B799A0FFF4D8980CBF2:2
308E814601245EB388B9547B0B09E9632A1:20
30929138F82703CFD124D067D757C493F0B:2
309F629614755453DBE44997FC7FB56625B:1
30ABB3E1761DB84DE6C0E3FCF2119B1D24E:1
30AEC64F672C7992423CF7DE522530E7BA4:1';

    public function setUp()
    {
        parent::setUp();
        $config = Hibp::loadConfig();

        $this->pwnedPassword = new PwnedPassword($config);
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->pwnedPassword = null;
    }

    /** @test */
    public function instance_of_class_should_be_a_PwnedPassword()
    {
        $this->assertInstanceOf(PwnedPassword::class, $this->pwnedPassword);
    }

    /** @test */
    public function successful_lookup_should_return_an_integer_count()
    {
        $mock = \Mockery::mock(PwnedPassword::class);
        $mock->shouldReceive('getStatusCode')->once()->andReturn(200);
        $mock->shouldReceive('lookup')->once()->andReturn(100);

        $this->assertEquals(200, $mock->getStatusCode());

        $response = $mock->lookup('password');

        $this->assertInternalType('int', $response);
        $this->assertGreaterThan(0, $response);
    }

    /** @test */
    public function successful_range_lookup_should_return_positive_integer()
    {
        $mock = \Mockery::mock(PwnedPassword::class);
        $mock->shouldReceive('range')
            ->once()
            ->withArgs(['5baa6', '5BAA61E4C9B93F3F0682250B6CF8331B7EE68FD8'])
            ->once()
            ->andReturn(100);

        $response = $mock->range('5baa6', '5BAA61E4C9B93F3F0682250B6CF8331B7EE68FD8');

        $this->assertInternalType('int', $response);
        $this->assertGreaterThan(0, $response);
    }

    /** @test */
    public function failed_range_lookup_should_return_zero()
    {
        $mock = \Mockery::mock(PwnedPassword::class);
        $mock->shouldReceive('range')
            ->once()
            ->withArgs(['00000', '0000000000000000000000000000000000000000'])
            ->andReturn(0);

        $response = $mock->range('00000', '0000000000000000000000000000000000000000');

        $this->assertEquals(0, $response);
    }

    /** @test */
    public function successful_range_data_lookup_should_return_collection()
    {
        $rangeData = $this->getRangeData('5baa6', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8');

        $mock = \Mockery::mock(PwnedPassword::class);
        $mock->shouldReceive('rangeData')
            ->once()
            ->withArgs(['5baa6', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8'])
            ->andReturn($rangeData);

        $this->assertInstanceOf(Collection::class, $rangeData);
    }

    /**
     * @param $hashSnippet string
     * @param $hash string
     *
     * @return Collection
     */
    protected function getRangeData($hashSnippet, $hash): Collection
    {
        $results = collect(explode("\r\n", self::RANGE_DATA));

        return $results->map(function($hashSuffix) use ($hashSnippet, $hash) {
            list($suffix, $count) = explode(':', $hashSuffix);
            $fullHash = sprintf('%s%s', $hashSnippet, $suffix);

            return collect([
                $fullHash => [
                    'hashSnippet' => $fullHash,
                    'count' => (int)$count,
                    'matched' => $fullHash == $hash,
                ],
            ]);
        });
    }
}
