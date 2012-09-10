<?php

/**
* This file contains the osMulti class which initializes the plugin and provides global variables/methods for use in the rest of the plugin.
*
* @package Organize Series Multi
* @since 0.1
*/

//TODO
/*
* need to make sure that we show a warning on install that let's people know that installing this plugin is irreversible (in other words they can't go from multiple series back to Organize Series Core. 
*/
if (!class_exists('osMulti') ) {

class osMulti {

	var $settings;
	var $version = OS_MULTI_VER;
	var $os_multi_domain = 'organize-series-multiples';
	var $message_id = 1;
	
	//__constructor
	function osMulti() {
		
		// always check to make sure organize series is running first //
		add_action('plugins_loaded', array(&$this, 'orgseries_check'));
		
		//initial setup stuff
		add_action('activate_organize-series-multiples/organize-series-multiples.php', array(&$this, 'install'));
		add_action('plugins_loaded', array(&$this, 'add_settings'));
		add_action('init', array(&$this, 'register_textdomain'));
		add_action('init', array(&$this, 'register_scripts_styles'));
		
		//replacing organize series hooks/filters
		add_action('quick_edit_custom_box', array(&$this, 'inline_edit'), 9, 2);
		add_action('manage_posts_custom_column', array(&$this, 'posts_custom_column_action'), 12, 2);
		add_action('admin_print_scripts-edit.php', array(&$this, 'inline_edit_js'));
		add_action('admin_print_scripts-post.php', array(&$this, 'add_series_js'));
		add_action('admin_print_scripts-post-new.php', array(&$this, 'add_series_js'));
		add_action('admin_print_styles-edit.php', array(&$this, 'inline_edit_css'));
		add_action('wp_ajax_add_series', array(&$this, 'ajax_series'));
		add_action('add_meta_boxes', array(&$this, 'meta_box'));
		add_action('delete_series', array(&$this, 'delete_series'), 10, 2);
		
		//hooking into Organize Series
		add_filter('orgseries_part_key', array(&$this, 'part_key'), 10, 2);
		add_filter('orgseries_sort_series_page_where', array(&$this, 'sort_series'));
		
	}
	
	function orgseries_check() {
		if (!class_exists('orgSeries') ) {
			add_action('admin_notices', array(&$this, 'orgseries_warning'));
			add_action('admin_notices', array(&$this, 'orgseries_deactivate'));
			return;
		}
		
		$os_version = get_option('org_series_version');
		if ( $os_version < '2.3' ) {
			$this->message_id = 2;
			add_action('admin_notices', array(&$this, 'orgseries_warning'));
			add_action('admin_notices', array(&$this, 'orgseries_deactivate'));
		}
		return;
	}
	
	function orgseries_warning() {
		if ( $this->message_id == 1 ) {
			$msg = '<div id="wpp-message" class="error fade"><p>'.__('The <strong>Organize Series Multiples</strong> addon for Organize Series requires the Organize Series plugin to be installed and activated in order to work.  Addons won\'t activate until this condition is met.', 'organize-series-multiples').'</p></div>';
		}
		
		if ( $this->message_id == 2 ) {
			$msg = '<div id="wpp-message" class="error fade"><p>' .__('The <strong>Organize Series Multiples</strong> addon for Organize Series requires version 2.3 or greater of Organize Series to be installed and activated in order to work.  Organize Series Multiples has been deactivated.', 'organize-series-multiples').'</p></div>';
		}
		echo $msg;
	}
	
	function orgseries_deactivate() {
		deactivate_plugins('organize-series-multiples/organize-series-multiples.php', true);
	}
	
	function install() {
		//let's do a version check (and make sure if this is version 0.1 that we do the update
		$old_version = get_option('os_multi_version');
		
		if ( empty($old_version) || $this->version == '0.1' ) {
			if ( $old_version != $this->version || $this->version == '0.1' ) $this->update($oldversion);
		}
		
		update_option('os_multi_version', $this->version);
	}
	
	function register_scripts_styles() {
		$url = OS_MULTI_URL.'js/';
		$c_url = OS_MULTI_URL.'css/';
		wp_register_script('inline-edit-series-multiples', $url.'inline-edit-series-multiples.js');
		wp_register_script('series-multiples-add', $url.'series-multiples.js', array('jquery', 'jquery-ui-core', 'jquery-color'));
		wp_localize_script('series-multiples-add', 'seriesL10n', array(
			'add' => esc_attr(__('Add', 'organize-series-multiples')),
			'how' => __('To remove a post from series, just deselect any checkboxes'),
			'addnonce' => wp_create_nonce('add-series-nonce')
			));
		wp_register_style('series-multiples-inline-edit', $c_url.'series-multiples-edit-php.css');
	}
	
	//do stuff that needs to be done with a version upgrade (if necessary)
	function update($version) {
		global $wpdb;
		if ( $version == '0.1' || empty($version) ) {
			$import_chk = get_option('os_multi_import');
			if ( empty($import_chk) || !$import_chk ) {
				//this means we need to do an import to convert the SERIES_PART meta_key's in organize Series core to the SERIES_PART key for multiples (not reversible!)
				
				//get list of posts that contain the meta key SERIES_PART_KEY
				$query = "SELECT p.ID, t.term_id, pm.meta_value FROM $wpdb->posts AS p LEFT JOIN $wpdb->postmeta AS pm ON p.ID = pm.post_id LEFT JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id LEFT JOIN $wpdb->term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id LEFT JOIN $wpdb->terms AS t ON tt.term_id = t.term_id WHERE pm.meta_key = '".SERIES_PART_KEY."' AND tt.taxonomy = 'series'";
				$posts = $wpdb->get_results($query);
				//let's cycle through the posts and update the meta_keys to the new format.
				if ( empty($posts) ) return; //get out there's no posts to update.
				foreach ($posts as $post) {
					$meta_key = SERIES_PART_KEY . '_' . $post->term_id;
					$meta_value = $post->meta_value;
					add_post_meta($post->ID, $meta_key, $meta_value);
				}
				add_option('os_multi_import', true);
			}
		}
		return;
	}
	
	function add_settings() {
		//we'll use this for setting up the defaults for the os_multi options.  However there are none yet so let's get out of here.
		return;
	}
	
	function register_textdomain() {
		$dir = basename(dirname(__FILE__)).'/lang';
		load_plugin_textdomain('organize-series-multiples', false, $dir);
	}
	
	function part_key($part_key, $series_id) {
		$key = $part_key.'_'.$series_id;
		return $key;
	}
	
	function inline_edit( $column_name, $type ) {
		//'organize-series-multiples'
	if ( $type == 'post' && $column_name == 'series' ) {
		?>
		<fieldset class="inline-edit-col-right"><div class="inline-edit-col">
			<div class="inline_edit_series_">
				<span class="title inline-edit-categories-label"><?php _e('Series:', 'organize-series-multiples'); ?></span>
				<ul id="series-checklist-ul" class="cat-checklist series-checklist">
					<?php $this->wp_series_checklist(null, 'name=post_series&class=post_series_select&id=series_part_'); ?>
				</ul>
				<input type="hidden" name="series_post_id" class="series_post_id"  />
			
			
		</div></div></fieldset>
			<?php
		}
	}
	
	function inline_edit_js() {
		wp_enqueue_script('inline-edit-series-multiples');
	}
	
	function add_series_js() {
		wp_enqueue_script('series-multiples-add');
	}
	
	function inline_edit_css() {
		wp_enqueue_style('series-multiples-inline-edit');
	}
	
	function wp_series_checklist( $post_id = 0, $args = array() ) {
		$defaults = array(
			'selected_series' => false,
			'popular_series' => false,
			'walker' => null,
			'taxonomy' => 'series',
			'checked_ontop' => true,
			'name' => 'post_series',
			'id' => '',
			'class' => 'postform'
		);
		
		$args = wp_parse_args($args, $defaults);
		extract( $args, EXTR_SKIP );

		$post_id = (int) $post_id;
		
		if ( empty($walker) || !is_a($walker, 'Walker') ) 
			$walker = new Walker_SeriesChecklist;
		
		$tax = get_taxonomy($taxonomy);
		$args['disabled'] = !current_user_can($tax->cap->assign_terms);
		
		
		$args['selected_series'] = (array) $selected_series;
		
		
		if ( $post_id ) {
			$args['selected_series'] = wp_get_object_terms($post_id, $taxonomy, array_merge($args, array('fields' => 'ids')));
		
		} else {
			$args['selected_series'] = array();
		}
			
		
		if ( is_array( $popular_series ) )
			$args['popular_series'] = $popular_series;
		else
			$args['popular_series'] = get_terms( $taxonomy, array( 'fields' => 'ids', 'orderby' => 'count', 'order' => 'DESC', 'number' => 10, 'hierarchical' => false ) );
			
		$series = (array) get_terms($taxonomy, array('get' => 'all'));
		
		//need to send an array of series_parts to the assembler as well.
		if ( !empty($post_id) ) {
			foreach ($series as $ser) {
				$series_part = wp_series_part($post_id, $ser->term_id);
				$sp[$ser->term_id] = $series_part;
			}
		} else {
			$sp = array();
		}	
		
		$args['series_parts'] = $sp;
					
		if ( $checked_ontop ) {
			//Post process $series rather than adding an exclude to the get_terms() query to keep the query the same across all posts (for any query cache)
			$checked_series = array();
			$keys = array_keys( $series );
			foreach( $keys as $k ) {
				if ( in_array( $series[$k]->term_id, $args['selected_series'] ) ) {
					$checked_series[] = $series[$k];
					unset( $series[$k] );
				}
			}
			
			// Put checked series on top
			echo call_user_func_array(array(&$walker, 'walk'), array($checked_series, 0, $args));
		}
		
		//Then the rest of them
		echo call_user_func_array(array(&$walker, 'walk'), array($series, 0, $args));
	}
	
	function sort_series($where) {
		global $wpdb;
		$series = get_query_var(SERIES_QUERYVAR);
		$ser_id = is_numeric($series) ? (int) $series : get_series_ID($series);
		$part_key = SERIES_PART_KEY.'_'.$ser_id;
		$where = $wpdb->prepare(" AND orgmeta.meta_key = %s ", $part_key);
		return $where;
	}
	
	function ajax_series() {
		$response = array();
	
		if ( !current_user_can( 'manage_series' ) )
			$response['error'] = __('Sorry but you don\'t have permission to add series', 'organize-series');

		if ( ! check_ajax_referer ( 'add-series-nonce', 'addnonce', false ) ) {
			$response['error'] = 'Sorry but security check failed';
		}
		$new_nonce = wp_create_nonce('add-series-nonce');

		$name = $_POST['newseries'];

		$series_name = trim($name);
		if ( !$series_nicename = sanitize_title($series_name) )
			$response['error'] = __('The name you picked isn\'t sanitizing correctly. Try something different.', 'organize-series');
		if ( !$series_id = series_exists( $series_name ) ) {
			$ser_id = wp_create_single_series( $series_name );
			$series_id = $ser_id['term_id'];
		} else {
			$response['error'] = __('Hmm... it looks like there is already a series with that name. Try something else', 'organize-series');
		}

		$series_name = esc_html(stripslashes($series_name));

		if ( !isset($response['error'] ) ) {
			$response = array(
				'id' => $series_id,
				'html' => "\n<li id='series-{$series_id}' class='series-added-indicator'>".'<span class="to_series_part"><input id="series-part-'.$series_id.'" type="text" class="series_part" size="3" value="" name="series_part['.$series_id.']" />' ."<label for='in-series-{$series_id}' class='selectit'>" . '<input value="'. $series_id . '" type="checkbox" name="post_series[]" id="in-series-'. $series_id . '" checked /><span class="li-series-name">' . $series_name . '</span></label></li>',
				'new_nonce' => $new_nonce,
				'error' => false
				);
		}
		echo json_encode($response);
		exit();
	} 
	
	function delete_series($series_ID, $taxonomy_id) {
		global $wpdb;
		seriesicons_delete($series_ID);
		$series_part = SERIES_PART_KEY.'_'.$series_ID;
		$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->post_meta WHERE meta_key LIKE %s", $series_part) );
		return;
	}
	
	function meta_box() {
		remove_meta_box('seriesdiv', 'post', 'side'); //remove default meta box included with Organize Series Core plugin
		add_meta_box('newseriesdiv', __('Series', 'organize-series-multiples'), array(&$this, 'add_meta_box'), 'post', 'side');
	}
	
	function add_meta_box() {
		global $post, $postdata, $content;
		$id = isset($post) ? $post->ID : $postdata->ID;
		if (current_user_can('manage_series')) {
		?>
		<p id="jaxseries"></p>
		<?php } ?>
		<span id="series-ajax-response"></span>
		<ul id="serieschecklist" class="list:series serieschecklist form-no-clear">
				<?php $this->wp_series_checklist($post->ID); ?>
		</ul>
		
			<p id="part-description"><?php _e('The part this post is in a series is listed next to each series name.  If you select a series and leave the part number blank then the post will automatically be appended to the end of the series.', 'organize-series-multiples'); ?></p>
		<?php
	}
	
	function posts_custom_column_action($column_name, $id) {
		$seriesid = null;
		$series_part = null;
		$series_name = null;
		if ($column_name == 'series') {
			$column_content = "<div class=\"series_column\">\n";	
			if ( $series = get_the_series($id, false) ) {
				$column_content .= "\t<ul class=\"series-column\">\n";
				foreach ( $series as $ser ) {
					$id_arr[] = $seriesid =  $ser->term_id;
					$sname_arr[] = $series_name = $ser->name;
					$spart_arr[] = $series_part = wp_series_part($id, $seriesid);
					$series_link = get_series_link($series_name);
					$count = $ser->count;
					$column_content .= "\t\t<li class=\"series-column-li\">";
					
					$draft_posts = get_posts( array(
						'post_type'	=> 'post',
						'post_status' => array('draft', 'future', 'pending'),
						'taxonomy'	=> 'series',
						'term'	=> $series_name
					) );
					$count_draft_posts = count($draft_posts);
					$drafts_included = '';
					if($count_draft_posts != 0){
						$all_serie_posts = $count_draft_posts+$count;
						$drafts_included = " ($all_serie_posts)";
					}
                                        
					if ( get_post_status($id) == 'publish') {
						$column_content .= sprintf(__('<a href="%1$s" title="%2$s">%3$s</a> %4$s of %5$s%6$s', $this->os_multi_domain), $series_link, $series_name, $series_name, $series_part, $count, $drafts_included);
						
					} else {
						$column_content .= sprintf(__('<a href="%1$s" title="%2$s">%3$s</a> (Currently as %4$s)', $this->os_multi_domain), $series_link, $series_name, $series_name, $series_part);
					}
					
					$column_content .= "</li>\n";
				}
				
				$column_content .= "\t</ul>\n";
				$seriesids = implode(',', $id_arr);
				$series_names = implode(',', $sname_arr);
				//$series_parts = implode(',', $spart_arr);
				$column_content .= "\t" . '<div class="hidden" id="inline_series_' . $id . '">';
				$column_content .= "\n\t\t" . '<div class="series_inline_edit">' . $seriesids . '</div>';
				
				for ($i=0; $i < count($id_arr); $i++ ) {
					$column_content .= "\n\t\t" . '<div class="series_inline_part_'.$id_arr[$i].'">' . $spart_arr[$i] . '</div>';
				}
				$column_content .= "\n\t\t" . '<div class="series_post_id">' . $id . '</div>';
				$column_content .= "\n\t\t" . '<div class="series_inline_name">' . $series_names . '</div>';
				$column_content .= "\n\t</div>\n";
				
			} else {
				$column_content .= "\t" . '<div class="hidden" id="inline_series_' . $id . '">';
				$column_content .= "\n\t\t" . '<div class="series_inline_edit"></div>';
				$column_content .= "\n\t\t" . '<div class="series_inline_part"></div>';
				$column_content .= "\n\t\t" . '<div class="series_post_id">' . $id . '</div>';
				$column_content .= "\n\t\t" . '<div class="series_inline_name"></div>';
				$column_content .= "\n\t</div>\n";
			}
			
			$column_content .= "</div>";
			echo $column_content;
		} 
	}

} //end class osMulti
} //end class check

/** 
* Directly taken and modified from WordPress -> Walker_Category_Checklist.  Needed to do this so I can insert the fields for setting the series part when there are multiple series.
*/
class Walker_SeriesChecklist extends Walker {
	var $tree_type = 'series';
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id');
	var $alt = 0;
	
	function start_el(&$output, $series, $depth, $args) {
		extract($args);
		$class = 'series-li ';
		$class .= in_array( $series->term_id, $popular_series ) ? 'popular-category' : '';
		$class .= ( $this->alt&1 ) ? ' odd' : '';
		$class = 'class="'.$class.'"';
		$series_part = array_key_exists($series->term_id, $series_parts) ? $series_parts[$series->term_id] : '';
		$output.= "\n<li id='series-{$series->term_id}'$class>" . '<span class="to_series_part"><input id="series-part-'.$series->term_id.'" type="text" class="series_part" size="3" value="'.$series_part.'" name="series_part['.$series->term_id.']" /></span><label class="selectit"><input class="series-li-input" value="' . $series->term_id . '" type="checkbox" name="'.$name.'[]" id="in-series-' . $series->term_id .'"' . checked( in_array($series->term_id, $selected_series ), true, false ) . disabled( empty( $args['disabled']), false, false ) . ' /> ' . esc_html( apply_filters('list_series', $series->name, $series )) . '</label>';
		$this->alt++;
	}

	function end_el(&$output, $series, $depth, $args) {
		$output .= "</li>\n";
	}
} //end Walker_SeriesChecklist class
