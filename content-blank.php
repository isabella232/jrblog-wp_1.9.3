<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package WordPress
 * @subpackage jrConway.jrBlog
 * @since jrBlog 1.9.4
 */
?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<a id="heading">&nbsp;</a>

		<div class="entry-content">
			<?php the_content(); ?>
			<?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'jrblog' ), 'after' => '</div>' ) ); ?>
		</div><!-- .entry-content -->
		<footer class="entry-meta">
			<?php edit_post_link( __( 'Edit', 'jrblog' ), '<span class="edit-link">', '</span>' ); ?>
		</footer><!-- .entry-meta -->
	</article><!-- #post -->
