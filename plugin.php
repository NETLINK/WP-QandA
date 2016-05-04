<?php

if ( !defined( 'POST_EXCERPT_LENGTH' ) )
{
	define( 'POST_EXCERPT_LENGTH', 100 );
}

class qanda
{
	function __construct()
	{
		add_action( 'init', array( $this, '_register_post_type' ) );
		
		add_shortcode( 'questions', array( $this, '_questions' ) );
		add_shortcode( 'answers', array( $this, '_answers' ) );
	}
	
	function _register_post_type()
	{
		// creating (registering) the custom type
		register_post_type( 'qanda', /* (http://codex.wordpress.org/Function_Reference/register_post_type) */
			// let's now add all the options for this post type
			array( 'labels' => array(
				'name' => __( 'FAQs', 'qanda' ), /* This is the Title of the Group */
				'singular_name' => __( 'FAQ', 'qanda' ), /* This is the individual type */
				'all_items' => __( 'All FAQs', 'qanda' ), /* the all items menu item */
				'add_new' => __( 'Add New', 'qanda' ), /* The add new menu item */
				'add_new_item' => __( 'Add New FAQ', 'qanda' ), /* Add New Display Title */
				'edit' => __( 'Edit', 'qanda' ), /* Edit Dialog */
				'edit_item' => __( 'Edit FAQs', 'qanda' ), /* Edit Display Title */
				'new_item' => __( 'New FAQ', 'qanda' ), /* New Display Title */
				'view_item' => __( 'View FAQ', 'qanda' ), /* View Display Title */
				'search_items' => __( 'Search FAQs', 'qanda' ), /* Search Custom Type Title */ 
				'not_found' =>  __( 'Nothing found in the Database.', 'qanda' ), /* This displays if there are no entries yet */ 
				'not_found_in_trash' => __( 'Nothing found in Trash', 'qanda' ), /* This displays if there is nothing in the trash */
				'parent_item_colon' => ''
				), /* end of arrays */
				'description' => __( 'Frequently asked questions', 'qanda' ), /* Custom Type Description */
				'public' => true,
				'publicly_queryable' => true,
				'exclude_from_search' => false,
				'show_ui' => true,
				'query_var' => true,
				'menu_position' => 8, /* this is what order you want it to appear in on the left hand side menu */ 
				'menu_icon' => plugins_url( 'qanda.png', __FILE__ ), /* the icon for the custom post type menu */
				'rewrite'	=> array( 'slug' => 'answers', 'with_front' => false ), /* you can specify its url slug */
				'has_archive' => false,//'faqs', /* you can rename the slug here */
				'capability_type' => 'post',
				'hierarchical' => false,
				/* the next one is important, it tells what's enabled in the post editor */
				'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'revisions', 'sticky' )
			) /* end of options */
		); /* end of register post type */
		
		/* this adds your post categories to your custom post type */
		register_taxonomy_for_object_type( 'category', 'qanda' );
		/* this adds your post tags to your custom post type */
		register_taxonomy_for_object_type( 'post_tag', 'qanda' );
		
	}
	
	function _questions( $atts )
	{
		query_posts( 'post_type=qanda&posts_per_page=-1&order=ASC' );
		
		$o = '';
		
		if ( have_posts() ) :
		
			$o .= "<ul class=\"qanda-questions\">\n";
		
			while ( have_posts() ) :
			
				the_post();
		
				$o .= '<li><a href="#faq-' . get_the_id() . '">' . get_the_title() . "</a></li>\n";
			
			endwhile;
			$o .= "</ul>\n";
		endif;
		
		wp_reset_query();
		
		return $o;
	}
	
	function _answers( $atts )
	{
		query_posts( 'post_type=qanda&posts_per_page=-1&order=ASC' );
		
		$o = '';
		
		if ( have_posts() ) :
		
			$o .= '<div class="qanda-answers">' . "\n";
		
			while ( have_posts() ) :
			
				the_post();
				
				$content = get_the_content();
				
				$o .= '<article id="faq-' . get_the_ID() . '">' . "\n";
				$o .= "\t<header><h3>" . get_the_title() . "</h3></header>\n";
				if ( str_word_count( strip_tags( $content ) ) > POST_EXCERPT_LENGTH - 5 ) $o .= "\t<p>" . get_the_excerpt() . "</p>\n";
				else $o .= "\t" . self::get_the_content() . "\n";
				$o .= "\t" . '<footer><p><a href="#top">Back to top</a></p>' . "\n";
				$o .= get_the_tags( '<p class="tags"><span class="tags-title">Tags:</span> ', ', ', '</p>' );
				$o .= "\t</footer>\n";
				$o .= "</article>\n";
					
			endwhile;
			$o .= "</div>\n";
		endif;
		
		wp_reset_query();
		
		return $o;
	}
	
	function get_the_content( $more_link_text = '(more...)', $stripteaser = 0, $more_file = '' )
	{
		$content = get_the_content( $more_link_text, $stripteaser, $more_file );
		$content = apply_filters( 'the_content', $content );
		$content = str_replace( ']]>', ']]&gt;', $content );
		return $content;
	}

}

new qanda;

?>