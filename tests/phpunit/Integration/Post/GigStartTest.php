<?php
namespace Bandstand\Test\Integration\Post;

use Bandstand_Post_Gig;


class GigStartTest extends \WP_UnitTestCase {
	protected $gig;

	public function setUp() {
		parent::setUp();

		$gig = $this->getMockBuilder( '\Bandstand_Post_Gig' )
			->setMethods( array( 'get_site_timezone_id' ) )
			->getMock();

		$gig->method( 'get_site_timezone_id' )
			->willReturn( 'America/Los_Angeles' );

		$gig->start_date = '2015-06-01';
		$gig->start_time = '08:00:00';

		$this->gig = $gig;
	}

	public function test_gig_properties() {
		$data = $this->get_gig_data();
		$gig = new Bandstand_Post_Gig( $data );

		$this->assertEquals( $data['start_date'], $gig->start_date );
		$this->assertEquals( $data['start_time'], $gig->start_time);
	}

	public function test_get_start_date() {
		$data = $this->get_gig_data();
		$gig = new Bandstand_Post_Gig( $data );
		$this->assertEquals( $data['start_date'], $gig->get_start_date() );
	}

	public function test_has_start_time() {
		$gig = new Bandstand_Post_Gig();
		$this->assertFalse( $gig->has_start_time() );

		$gig->start_time = '8:00:00';
		$this->assertTrue( $gig->has_start_time() );
	}

	public function test_get_start_time() {
		$gig = new Bandstand_Post_Gig();
		$this->assertNull( $gig->get_start_time() );

		$gig->start_time = '8:00:00';
		$this->assertEquals( '8:00:00', $gig->get_start_time() );
	}

	public function test_get_start_datetime() {
		$datetime = $this->gig->get_start_datetime();
		$this->assertInstanceOf( 'DateTime', $datetime );
		$this->assertEquals( 'America/Los_Angeles', $datetime->getTimezone()->getName() );
	}

	protected function get_gig_data() {
		return array(
			'start_date' => '2015-06-01',
			'start_time' => '8:00:00',
		);
	}
}
