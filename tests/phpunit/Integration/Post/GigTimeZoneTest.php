<?php
namespace Bandstand\Test\Integration\Post;

use Bandstand_Post_Gig;


class GigTimeZoneTest extends \WP_UnitTestCase {
	protected static $gig_id;
	protected static $venue_id;

	public static function wpSetUpBeforeClass( $factory ) {
		self::$gig_id = self::factory()->post->create( array(
			'post_title'  => 'Gig Title',
			'post_type'   => 'bandstand_gig',
			'post_status' => 'publish',
		) );

		self::$venue_id = self::factory()->post->create( array(
			'post_title'  => 'Venue Name',
			'post_type'   => 'bandstand_venue',
			'post_status' => 'publish',
		) );

		update_post_meta( self::$venue_id, 'bandstand_timezone_id', 'America/New_York' );
	}

	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		delete_post_meta( self::$gig_id, 'bandstand_timezone_id' );
		delete_post_meta( self::$gig_id, 'bandstand_venue_id' );
		delete_post_meta( self::$gig_id, 'bandstand_venue_guid' );

		parent::tearDown();
	}

	public function test_has_timezone() {
		$gig = new Bandstand_Post_Gig();
		$this->assertFalse( $gig->has_timezone() );
	}

	public function test_get_timezone_id_from_site() {
		$gig = new Bandstand_Post_Gig();
		$this->assertEquals( 'America/Los_Angeles', $gig->get_timezone_id() );
	}

	public function test_get_timezone_id_from_post() {
		update_post_meta( self::$gig_id, 'bandstand_timezone_id', 'America/Chicago' );
		$gig = get_bandstand_gig( self::$gig_id );

		$this->assertTrue( $gig->has_timezone() );
		$this->assertEquals( 'America/Chicago', $gig->get_timezone_id() );
	}

	public function test_get_timezone_id_from_venue() {
		$gig = get_bandstand_gig( self::$gig_id );
		$this->assertEquals( 'America/Los_Angeles', $gig->get_timezone_id() );

		$gig = $gig->set_venue( self::$venue_id );
		$this->assertEquals( 'America/New_York', $gig->get_timezone_id() );
	}
}
