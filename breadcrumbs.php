<?php

/**
 * Breadcrumbs
 *
 * @author Mafel John Cahucom <mafeljohn.timkang.cahucom070596@gmail.com>
 * @since 1.0.0
 */

final class Breadcrumbs {

	// separator
	private $separator = '/';

	// homepage title
	private $home_title = 'Home';

	// search prefix
	private $search_prefix = 'Search result for';

	// archive prefix
	private $archive_prefix = 'Archives for ';


	/**
	 * Initialize the arguments for breadcrumbs
	 * @param  array $args  list of arguments
	 * @return class
	 */
	public function init( $args = [] ) {

		if( isset( $args['separator'] ) ) {
			$this->separator  = $args['separator'];
		}

		if( isset( $args['home_title'] ) ) {
			$this->home_title = $args['home_title'];
		}

		if( isset( $args['search_prefix'] ) ) {
			$this->search_prefix = $args['search_prefix'];
		}

		if( isset( $args['archive_prefix'] ) ) {
			$this->archive_prefix = $args['archive_prefix'];
		}

		return $this;
	}


	/**
	 * Use breadcrumbs
	 */
	public function use() {


		if( ! is_front_page() ) {


			// open breadcrumbs
			$output = '<div class="breadcrumbs">';

			// home
			$output .= $this->get_home() . $this->get_separator();


			// category post type: post
			if( is_category() ) {
				$output .= $this->get_post_link( get_option('page_for_posts') ).
						   $this->get_separator().
						   $this->get_current_link( single_cat_title( $this->archive_prefix, false ) );
			}


			// archive post type: post
			if( is_archive() ) {
				$date = array(
			        'd' => get_the_date( 'd' ),
			        'm' => get_the_date( 'm' ),
			        'y' => get_the_date( 'Y' )
			    );

				if( is_day() ) {
					$output .= $this->get_date_link( 'year', $date ).
						   	   $this->get_separator().
						   	   $this->get_date_link( 'month', $date ).
						       $this->get_separator().
						       $this->get_current_link( $this->archive_prefix .' '. $date['d'] );
				}elseif( is_month() ) {
					$output .= $this->get_date_link( 'year', $date ).
						   	   $this->get_separator().
						   	   $this->get_current_link( $this->archive_prefix .' '. $date['m'] );
				}elseif( is_year() ) {
					$output .= $this->get_current_link( $this->archive_prefix .' '. $date['y'] );
				}
			}


			// taxonomy
			if( is_tax() ) {
				// post type - portfolio
				if( get_post_type( get_the_ID() ) == 'portfolio' ) {
					$term = get_the_terms( get_the_ID(), 'portfolio-tax' )[0];
					$output .= $this->get_post_link( 56 ).
							   $this->get_separator().
							   $this->get_current_link( $term->name );
				}
			}
			

			// page
			if( is_page() ) {
				$id = get_the_ID();
				// parent
				if( wp_get_post_parent_id( $id ) > 0 ) {
					$parent_id = wp_get_post_parent_id( $id );
					// grand parent
					if( wp_get_post_parent_id( $parent_id ) > 0 ) {
						$grand_parent_id = wp_get_post_parent_id( $parent_id );
						$output .= $this->get_post_link( $grand_parent_id ).
								   $this->get_separator().
								   $this->get_post_link( $parent_id ).
								   $this->get_separator().
								   $this->get_current_link( get_the_title() );
					}else{
						$output .= $this->get_post_link( $parent_id ).
								   $this->get_separator().
								   $this->get_current_link( get_the_title() );
					}
				}else{
					$output .= $this->get_current_link( get_the_title() );
				}
			}


			// static page : blog
			if( is_home() ) {
				$post_id = get_option( 'page_for_posts' );
				if( $post_id ) {
					$output .= $this->get_current_link( get_the_title( $post_id ) );
				}
			}


			// single page
			if( is_single() ) {
				if( get_post_type( get_the_ID() ) == 'post' ) {
					// post type: post
					$output .= $this->get_post_link( get_option('page_for_posts') ).
							   $this->get_separator().
							   $this->get_current_link( get_the_title() );
				}else{
					// post type: portfolio
					if( is_singular( 'portfolio' ) ) {
						$portfolio_page_id = 56;
						$output .= $this->get_post_link( $portfolio_page_id ).
								   $this->get_separator().
								   $this->get_current_link( get_the_title() );
					}
				}
			}


			// search page
			if( is_search() ) {
				$search_title = $this->search_prefix .' "'. get_search_query() .'"';
				$output .= $this->get_current_link( $search_title );
			}


			// 404 page
			if( is_404() ) {
				$output .= $this->get_current_link('Not Found');
			}


			// close breadcrumbs
			$output .= '</div>'; 
		}

		echo $output;
	}


	private function get_home() {
		$output = '<a class="brd__home" href="'. home_url() .'">'. $this->home_title .'</a>';
		return $output;
	}


	/**
	 * Output the separator with tag
	 * @return
	 */
	private function get_separator() {
		$output = '<span class="separator">'. $this->separator .'</span>';
		return $output;
	}

	/**
	 * Output link by given id
	 * @return [type] [description]
	 */
	private function get_post_link( $post_id ) {
		return $this->get_link( get_the_title( $post_id ), get_permalink( $post_id ) );
	}


	/**
	 * Output the link tag
	 */
	private function get_link( $title, $link ) {
		return '<a href="'. $link .'">'. $title .'</a>';
	}


	/**
	 * Output date link
	 */
	private function get_date_link( $format, $date ) {
		if( $format == 'day' ) {
			$output = $this->get_link( $date['d'], get_day_link( $date['y'], $date['m'], $date['d'] ) );
		}elseif( $format == 'month' ) {
			$output = $this->get_link( $date['m'], get_month_link( $date['y'], $date['m'] ) );
		}elseif( $format == 'year' ) {
			$output = $this->get_link( $date['y'], get_year_link( $date['y'] ) );
		}
		return $output;
	}


	/**
	 * Output the current link tag
	 */
	private function get_current_link( $title ) {
		return '<span class="current">'. $title .'</span>';
	}
}