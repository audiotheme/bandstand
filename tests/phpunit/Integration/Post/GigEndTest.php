<?php
namespace Bandstand\Test\Integration\Post;

use Bandstand_Post_Gig;


class GigEndTest extends \WP_UnitTestCase {
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

	public function test_get_end_date() {
		$data = $this->get_gig_data();
		$gig = new Bandstand_Post_Gig( $data );
		$this->assertEquals( $data['end_date'], $gig->get_end_date() );
	}

	public function test_has_end_time() {
		$gig = new Bandstand_Post_Gig();
		$this->assertFalse( $gig->has_end_time() );

		$gig->end_time = '8:00:00';
		$this->assertTrue( $gig->has_end_time() );
	}

	public function test_get_end_time() {
		$gig = new Bandstand_Post_Gig();
		$this->assertNull( $gig->get_end_time() );

		$gig->end_time = '8:00:00';
		$this->assertEquals( '8:00:00', $gig->get_end_time() );
	}

	public function test_get_end_datetime() {
		$datetime = $this->gig->get_end_datetime();
		$this->assertInstanceOf( 'DateTime', $datetime );
		$this->assertEquals( 'America/Los_Angeles', $datetime->getTimezone()->getName() );
	}

	protected function get_gig_data() {
		return array(
			'end_date' => '2015-06-01',
			'end_time' => '8:00:00',
		);
	}
}
