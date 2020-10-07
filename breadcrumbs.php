<?php

/**
 * Breadcrumb
 *
 * @author Mafel John Cahucom <mafeljohn.timkang.cahucom070596@gmail.com>
 * @since 1.0.0
 */

namespace App\Inc\Breadcrumb;

final class Breadcrumb {

	// separator
	private $separator = '/';

	// homepage title
	private $homeTitle = 'Home';

	// search prefix
	private $searchPrefix = 'Search result for';

	// archive prefix
	private $archivePrefix = 'Archives for ';


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
			$this->homeTitle = $args['home_title'];
		}

		if( isset( $args['search_prefix'] ) ) {
			$this->searchPrefix = $args['search_prefix'];
		}

		if( isset( $args['archive_prefix'] ) ) {
			$this->archivePrefix = $args['archive_prefix'];
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
			$output .= $this->getHome() . $this->getSeparator();


			// category or tag with post type: post
			if( is_category() || is_tag() ) {
				$output .= $this->getPostLink( get_option('page_for_posts') ).
						   $this->getSeparator().
						   $this->getCurrentLink( single_cat_title( $this->archivePrefix . ' ', false ) );
			}


			// archive post type: post
			if( is_archive() ) {
				$date = array(
			        'd' => get_the_date( 'd' ),
			        'm' => get_the_date( 'm' ),
			        'y' => get_the_date( 'Y' )
			    );

				if( is_date() ) {
					if( is_day() ) {
						$output .= $this->getPostLink( get_option('page_for_posts') ).
						   	   	   $this->getSeparator().
								   $this->getDateLink( 'year', $date ).
							   	   $this->getSeparator().
							   	   $this->getDateLink( 'month', $date ).
							       $this->getSeparator().
							       $this->getCurrentLink( $this->archivePrefix . ' day ' .' '. $date['d'] );
					}elseif( is_month() ) {
						$output .= $this->getPostLink( get_option('page_for_posts') ).
						   	       $this->getSeparator().
								   $this->getDateLink( 'year', $date ).
							   	   $this->getSeparator().
							   	   $this->getCurrentLink( $this->archivePrefix . ' month ' .' '. $date['m'] );
					}elseif( is_year() ) {
						$output .= $this->getPostLink( get_option('page_for_posts') ).
						   	   	   $this->getSeparator().
								   $this->getCurrentLink( $this->archivePrefix . ' year ' .' '. $date['y'] );
					}
				}elseif( is_author() ) {
					$output .= $this->getPostLink( get_option('page_for_posts') ).
						   	   $this->getSeparator().
							   $this->getCurrentLink( get_the_author_meta('display_name') );
				}
			}



			// taxonomy
			if( is_tax() ) {
				// post type - portfolio
				if( get_post_type( get_the_ID() ) == 'portfolio' ) {
					$term = get_the_terms( get_the_ID(), 'portfolio-tax' )[0];
					$output .= $this->getPostLink( 56 ).
							   $this->getSeparator().
							   $this->getCurrentLink( $term->name );
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
						$output .= $this->getPostLink( $grand_parent_id ).
								   $this->getSeparator().
								   $this->getPostLink( $parent_id ).
								   $this->getSeparator().
								   $this->getCurrentLink( get_the_title() );
					}else{
						$output .= $this->getPostLink( $parent_id ).
								   $this->getSeparator().
								   $this->getCurrentLink( get_the_title() );
					}
				}else{
					$output .= $this->getCurrentLink( get_the_title() );
				}
			}


			// static page : blog
			if( is_home() ) {
				$post_id = get_option( 'page_for_posts' );
				if( $post_id ) {
					$output .= $this->getCurrentLink( get_the_title( $post_id ) );
				}
			}


			// single page
			if( is_single() ) {
				if( get_post_type( get_the_ID() ) == 'post' ) {
					// post type: post
					$output .= $this->getPostLink( get_option('page_for_posts') ).
							   $this->getSeparator().
							   $this->getCurrentLink( get_the_title() );
				}else{
					// post type: portfolio
					if( is_singular( 'portfolio' ) ) {
						$portfolio_page_id = 56;
						$output .= $this->getPostLink( $portfolio_page_id ).
								   $this->getSeparator().
								   $this->getCurrentLink( get_the_title() );
					}
				}
			}


			// search page
			if( is_search() ) {
				$search_title = $this->searchPrefix .' "'. get_search_query() .'"';
				$output .= $this->getCurrentLink( $search_title );
			}


			// 404 page
			if( is_404() ) {
				$output .= $this->getCurrentLink('Not Found');
			}


			// close breadcrumbs
			$output .= '</div>'; 
		}

		echo $output;
	}


	private function getHome() {
		$output = '<a class="brd__home" href="'. home_url() .'">'. $this->homeTitle .'</a>';
		return $output;
	}


	/**
	 * Output the separator with tag
	 * @return
	 */
	private function getSeparator() {
		$output = '<span class="separator">'. $this->separator .'</span>';
		return $output;
	}

	/**
	 * Output link by given id
	 * @return [type] [description]
	 */
	private function getPostLink( $post_id ) {
		return $this->getLink( get_the_title( $post_id ), get_permalink( $post_id ) );
	}


	/**
	 * Output the link tag
	 */
	private function getLink( $title, $link ) {
		return '<a href="'. $link .'">'. $title .'</a>';
	}


	/**
	 * Output date link
	 */
	private function getDateLink( $format, $date ) {
		if( $format == 'day' ) {
			$output = $this->getLink( $date['d'], get_day_link( $date['y'], $date['m'], $date['d'] ) );
		}elseif( $format == 'month' ) {
			$output = $this->getLink( $date['m'], get_month_link( $date['y'], $date['m'] ) );
		}elseif( $format == 'year' ) {
			$output = $this->getLink( $date['y'], get_year_link( $date['y'] ) );
		}
		return $output;
	}


	/**
	 * Output the current link tag
	 */
	private function getCurrentLink( $title ) {
		return '<span class="current">'. $title .'</span>';
	}
}
