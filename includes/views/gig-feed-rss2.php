<?php
/**
 * Gigs RSS2 feed template.
 *
 * @package   Bandstand\Gigs
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

header( 'Content-Type: ' . feed_content_type( 'rss2' ) . '; charset=' . get_option( 'blog_charset' ), true );

// Servers with short tags enabled get confused if we don't output this with PHP.
echo '<?xml version="1.0" encoding="' . get_option( 'blog_charset' ) . '"?>';
?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:ev="http://purl.org/rss/1.0/modules/event/"
	xmlns:xcal="urn:ietf:params:xml:ns:xcal"
	xmlns:vCard="http://www.w3.org/2006/vcard/ns"
>

<channel>
	<title><?php wp_title_rss(); ?></title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php bloginfo_rss( 'url' ) ?></link>
	<description><?php bloginfo_rss( 'description' ) ?></description>
	<?php while ( have_posts() ) : the_post(); ?>
		<?php $gig = get_bandstand_gig(); ?>
		<item>
			<title><?php echo get_bandstand_gig_title(); ?></title>
			<link><?php the_permalink_rss() ?></link>
			<pubDate><?php echo mysql2date( 'D, d M Y H:i:s +0000', get_post_time( 'Y-m-d H:i:s', true ), false ); ?></pubDate>
			<guid isPermaLink="false"><?php the_guid(); ?></guid>

			<description><![CDATA[<?php echo get_bandstand_gig_rss_description(); ?>]]></description>
			<?php if ( strlen( $post->post_content ) > 0 ) : ?>
				<content:encoded><![CDATA[<?php the_content_feed( 'rss2' ) ?>]]></content:encoded>
			<?php endif; ?>

			<ev:startdate><?php echo $gig->format_start_date(); ?></ev:startdate>
			<ev:type>concert</ev:type>
			<xcal:dtstart><?php echo $gig->format_start_date(); ?></xcal:dtstart>

			<?php
			if ( ! empty( $gig->venue ) ) {
				echo get_bandstand_venue_vcard_rss( $gig->venue->ID );
			}
			?>
		</item>
	<?php endwhile; ?>
</channel>
</rss>
