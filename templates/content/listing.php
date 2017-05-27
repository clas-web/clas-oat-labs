<?php //vtt_print('default:content:listing'); ?>
<?php
global $wp_query, $wp, $post;
$is_mt = false;
$is_sf = false;
$search_term = "";
$sf_id = '/?sfid=1721&';
$sf_term = '_sft_';

if( function_exists('mt_is_archive') && function_exists('mt_is_search') && 
	( mt_is_archive() || mt_is_search() ) )
{
	$is_mt = true;
}

$is_mt = true;

if( isset( $_GET ) && isset( $_GET['sfid'] ) ) {
	$is_sf = true;
}

if ( is_tax() ) {
	$term_slug = get_query_var( 'term' );
	$taxname = get_query_var( 'taxonomy' );
	$term_link = site_url().$sf_id.$sf_term.$taxname.'='.$term_slug;
	wp_redirect( $term_link );
	exit;
} else if ( is_search() ) {
// 	$search_term = urlencode(get_search_query());
// 	$search_link = site_url().$sf_id.'_sf_s='.$search_term;
// 	wp_redirect( $search_link );
// 	exit;


} else {
	//$current_url = add_query_arg( $_SERVER['QUERY_STRING'], '', home_url( $wp->request ) );
	//echo $current_url
	//exit;
}
?>

<div class="page-title">
	
	<?php
	if( $is_mt )
	{
		$filter_terms = array();
		$current_filters = mt_get_current_filter_data();
		foreach( $current_filters['taxonomies'] as $taxname => $terms )
		{
			$taxonomy = get_taxonomy( $taxname );
			foreach( $terms as $term_slug )
			{
				$term = get_term_by( 'slug', $term_slug, $taxname );
				$link = labs_get_anchor(
					get_term_link( $term, $taxname ),
					$term->name,
					null,
					$term->name
				);
								
				if( $post->post_type === 'references') {					
					$term_link = site_url().$sf_filter_slug.$taxname.'='.$term_slug;
					$title = $term->name;
					$content = $term->name;
					$link = labs_get_anchor(
						$term_link,
						$term->name,
						null,
						$term->name
					);				
				
				}
							
				if( $term ) {
					$post_count = $wp_query->found_posts;
					echo '<div class="found-posts">'.$post_count.' posts found</div>';
				//	echo '<div class="current-filters"><h4>Current Selection</h4></div>';
					echo '<div class="breadcrumbs">' .
						$taxonomy->label .
						' &raquo; ' .
 						vtt_get_taxonomy_breadcrumbs( $term->term_id, $taxname ) .
 						$link .
						'</div>';
						
					$filter_terms[] = $term;
				}
			}
		}
	}
	elseif( is_a( get_queried_object(), 'WP_Term' ) )
	{
		$qo = get_queried_object();
		$post_count = $wp_query->found_posts;
		echo '<div class="found-posts">'.$post_count.' posts found</div>';
		echo '<div class="breadcrumbs">' .
			vtt_get_taxonomy_breadcrumbs( $qo->term_id, $qo->taxonomy ) .
			'</div>';

	}
	?>
	
	<?php
	if( vtt_has_page_listing_name() )
		echo '<div class="listing-name">'.vtt_get_page_listing_name().'</div>';
	?>

	<?php
	if( $is_mt )
	{
		
		if( count( $filter_terms ) > 0 ) {
			$term_names = array();
			foreach( $filter_terms as $term ) {
				$term_names[] = $term->name;
			}
			echo '<h1>';
			echo implode( ' / ', $term_names );
			echo '</h1>';
		}
		elseif( mt_is_filtered_archive() ) {
			echo '<h1>Filtered Results</h1>';
		}
		elseif( mt_is_combined_archive() ) {
			echo '<h1>Combined Results</h1>';
		}
		elseif( mt_is_filtered_search() ) {
			echo '<h1>Filtered Search Results</h1>';
		}
		elseif( mt_is_combined_search() ) {
			echo '<h1>Combined Search Results</h1>';
		}
		else {
			if( isset( $_GET ) && isset( $_GET['_sf_s'] ) ) {
				if (isset($search_term)) {
					echo '<h1>Filtered Search Results</h1>';
				}
			}
		}
		
	}
	elseif( !is_home() )
	{
		echo '<h1>'.vtt_get_page_title().'</h1>';
	}
	?>

</div>


<?php
if( $is_mt && count( $filter_terms ) > 0 ) {
	foreach( $filter_terms as $term ) {
		echo '<div class="description">' . apply_filters( 'term_description', $term->description ) . '</div>';
	}
}
elseif( vtt_has_page_description() ) {
	echo '<div class="description">' . vtt_get_page_description() . '</div>';
}
?>


<?php
if( !have_posts() ):

	echo '<p>No posts.</p>';

else:
	
	$number = 1;
	while( have_posts() ):
		the_post();
		if( $post->post_type === 'references') {
			$year = labs_get_acf_select_value( 'publication_year' );
		}
		?>
		<div <?php post_class(); ?>>

		<h2 class="entry-title"><a href="<?php echo get_permalink($post->ID); ?>">
		<?php if( $post->post_type === 'references'): ?>
			<?php echo $number++.") ".$year." - ".$post->post_title; ?></a>
		<?php else:?>
			<?php echo $post->post_title; ?></a>
		<?php endif; ?>
		</h2>
	
		<?php	
		vtt_get_template_part( 'listing', 'post', vtt_get_post_type(), $number );
		echo '</div>';
		//$number ++;
	endwhile;
 
	vtt_get_template_part( 'pagination', 'other', vtt_get_queried_object_type() );

endif;
?>

