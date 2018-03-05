<?php
/**
 * PwnedPasswords tests
 *
 * @author Ian <ian@ianh.io>
 * @since 28/02/2018
 */

use GuzzleHttp\Exception\GuzzleException;
use Icawebdesign\Hibp\Password\PwnedPassword;
use PHPUnit\Framework\TestCase;

class PwnedPasswordTest extends TestCase
{
    protected $pwnedPassword;

    public function setUp()
    {
        parent::setUp();
        $this->pwnedPassword = new PwnedPassword([
            'api_root'      => 'https://api.pwnedpasswords.com',
        ]);
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
    public function successful_lookup_should_return_status_code_200()
    {
        try {
            $this->pwnedPassword->lookup('password');
        } catch (GuzzleException $e) {
        }

        $this->assertEquals(200, $this->pwnedPassword->getStatusCode());
    }

    /** @test */
    public function successful_lookup_should_return_an_integer_count()
    {
        try {
            $response = $this->pwnedPassword->lookup('password');
        } catch (GuzzleException $e) {
            dd($e->getCode());
        }

        if (200 === $this->pwnedPassword->getStatusCode()) {
            $this->assertInternalType('int', $response);
        }
    }

    /** @test */
    public function failed_lookup_should_return_status_code_404()
    {
        try {
            $this->pwnedPassword->lookup('askjfsdjf908wrkwkafnkj');
        } catch (GuzzleException $e) {
            $this->assertEquals(404, $e->getCode());
        }
    }

    /** @test */
    public function successful_range_lookup_should_return_status_code_200()
    {
        try {
            $this->pwnedPassword->range(
                '5baa6',
                '5BAA61E4C9B93F3F0682250B6CF8331B7EE68FD8'
            );
        } catch (GuzzleException $e) {
            echo $e->getCode();
        }

        $this->assertEquals(200, $this->pwnedPassword->getStatusCode());
    }

    /** @test */
    public function successful_range_lookup_should_return_positive_integer()
    {
        try {
            $response = $this->pwnedPassword->range(
                '5baa6',
                '5BAA61E4C9B93F3F0682250B6CF8331B7EE68FD8'
            );
        } catch (GuzzleException $e) {
            echo $e->getCode();
        }

        $this->assertGreaterThan(0, $response);
    }

    /** @test */
    public function failed_range_lookup_should_return_zero()
    {
        try {
            $response = $this->pwnedPassword->range(
                '00000',
                '0000000000000000000000000000000000000000'
            );
        } catch (GuzzleException $e) {
            echo $e->getCode();
        }

        $this->assertEquals(0, $response);
    }

    /** @test */
    public function invalid_range_request_should_return_status_code_400()
    {
        try {
            $this->pwnedPassword->range(
                'ZXCVB',
                '0000000000000000000000000000000000000000'
            );
        } catch (GuzzleException $e) {
            $this->assertEquals(400, $e->getCode());
        }
    }
}
