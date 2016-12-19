<?php
namespace Bandstand\Test\Integration\Post;

use Bandstand_Post_Gig;


class GigTest extends \WP_UnitTestCase {
	public function setUp() {
		parent::setUp();
	}

	public function test_gig_from_post_id() {
		$post_id = $this->factory->post->create( array( 'post_type' => 'bandstand_gig' ) );
		$gig = get_bandstand_gig( $post_id );

		$this->assertTrue( $gig->has_post() );
		$this->assertEquals( $post_id, $gig->ID );
		$this->assertEquals( $post_id, $gig->get_post()->ID );
	}
}
