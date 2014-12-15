<?php

/*
	Plugin Name: Category Tree Filter
	Plugin URI: http://www.2dmonkey.com/ctf
	Description: Converts your post categories list into a collapsible tree with realtime filtering
	Author: 2DMonkey
	Version: 1.0.0
	Author URI: http://www.2dmonkey.com/
 */

if ( !defined( 'ABSPATH' ) ) die( __( 'Cannot load this file directly','category-tree-filter' ) );
if ( !defined( 'CTF_PATH' ) ) define( 'CTF_PATH', plugin_dir_path( __FILE__) );
if ( !defined( 'CTF_URL' ) ) define( 'CTF_URL', plugins_url( '', __FILE__ ) );

global $CTF;
$CTF = new CTF();
$CTF->setup();

class CTF {

	// Setup
	public function setup() {	
		add_action( 'admin_print_scripts', array( $this, 'scripts' ) );
		add_action( 'admin_print_styles', array( $this, 'styles' ) );
		add_action( 'add_meta_boxes', array( $this, 'metabox' ) );
	}

	// Scripts
	public function scripts() {
		wp_enqueue_script( 'ctf', CTF_URL . '/assets/ctf.js' );
		wp_enqueue_script( 'ctf_filter', CTF_URL . '/assets/filter.js' );			
	}
	
	// Styles
	public function styles() {
		wp_enqueue_style( 'ctf', CTF_URL . '/assets/ctf.css' );			
	}

	// Metabox
	public function metabox( $post_type ) {
		foreach ( get_object_taxonomies( $post_type ) as $tax_name ) {
			$taxonomy = get_taxonomy( $tax_name );
			if ( !$taxonomy->show_ui || !$taxonomy->hierarchical )
				continue;
			$label = isset( $taxonomy->label ) ? esc_attr( $taxonomy->label ) : $tax_name;
			remove_meta_box( $tax_name . 'div', $post_type, 'side' );
			add_meta_box( $tax_name . 'div', $label, array( $this, 'ctf_metabox' ), $post_type, 'side', 'default', array( 'taxonomy' => $tax_name ) );
		}
	}
	
	
	public function ctf_metabox( $post, $box ) {
		$defaults = array('taxonomy' => 'category');
		if (!isset($box['args']) || !is_array($box['args']))
			$args = array();
		else
			$args = $box['args'];
			extract(wp_parse_args($args, $defaults), EXTR_SKIP);
			$tax = get_taxonomy($taxonomy);
		?>
		<div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">
			
			<input class="widefat" id="categoryfilter" placeholder="Filter...">
			
			
			<ul id="<?php echo $taxonomy; ?>-tabs" class="category-tabs">
				<li class="tabs"><a href="#<?php echo $taxonomy; ?>-all" tabindex="3"><?php echo $tax->labels->all_items; ?></a></li>
				<li class="hide-if-no-js"><a href="#<?php echo $taxonomy; ?>-pop" tabindex="3"><?php _e('Most Used'); ?></a></li>
			</ul>
			
			<div id="<?php echo $taxonomy; ?>-pop" class="tabs-panel" style="display: none;">
				<ul id="<?php echo $taxonomy; ?>checklist-pop" class="categorychecklist form-no-clear" >
					<?php $popular_ids = wp_popular_terms_checklist($taxonomy); ?>
				</ul>
			</div>
			
			<div id="<?php echo $taxonomy; ?>-all" class="tabs-panel">			
				<?php
					$name = ( $taxonomy == 'category' ) ? 'post_category' : 'tax_input[' . $taxonomy . ']';
					echo "<input type='hidden' name='{$name}[]' value='0' />";
				?>
				<ul id="<?php echo $taxonomy; ?>checklist" data-wp-lists="list:<?php echo $taxonomy; ?>" class="<?php echo $taxonomy; ?>checklist form-no-clear">
					<?php wp_terms_checklist($post->ID, array('taxonomy' => $taxonomy, 'popular_cats' => $popular_ids, 'checked_ontop' => false)) ?>
				</ul>
			</div>
			
		<?php if (!current_user_can($tax->cap->assign_terms)) : ?>
			<p><em><?php _e('You cannot modify this taxonomy.'); ?></em></p>
		<?php endif; ?>
		
		<?php if (current_user_can($tax->cap->edit_terms)) : ?>
			<div id="<?php echo $taxonomy; ?>-adder" class="wp-hidden-children">
				<h4>
					<a id="<?php echo $taxonomy; ?>-add-toggle" href="#<?php echo $taxonomy; ?>-add" class="hide-if-no-js" tabindex="3">
						<?php
						/* translators: %s: add new taxonomy label */
						printf(__('+ %s'), $tax->labels->add_new_item);
						?>
					</a>
					<span style="float:right"><a href="#<?php echo $taxonomy; ?>-all" class="hide-if-no-js expand-all">Expand All</a></span>
				</h4>
				<p id="<?php echo $taxonomy; ?>-add" class="category-add wp-hidden-child">
					<label class="screen-reader-text" for="new<?php echo $taxonomy; ?>"><?php echo $tax->labels->add_new_item; ?></label>
					<input type="text" name="new<?php echo $taxonomy; ?>" id="new<?php echo $taxonomy; ?>" class="form-required form-input-tip" value="<?php echo esc_attr($tax->labels->new_item_name); ?>" tabindex="3" aria-required="true"/>
					<label class="screen-reader-text" for="new<?php echo $taxonomy; ?>_parent">
						<?php echo $tax->labels->parent_item_colon; ?>
					</label>
					<?php wp_dropdown_categories(array('taxonomy' => $taxonomy, 'hide_empty' => 0, 'name' => 'new' . $taxonomy . '_parent', 'orderby' => 'name', 'hierarchical' => 1, 'show_option_none' => '&mdash; ' . $tax->labels->parent_item . ' &mdash;', 'tab_index' => 3)); ?>
					<input type="button" id="<?php echo $taxonomy; ?>-add-submit" data-wp-lists="add:<?php echo $taxonomy ?>checklist:<?php echo $taxonomy; ?>-add" class="button <?php echo $taxonomy; ?>-add-submit" value="<?php echo esc_attr($tax->labels->add_new_item); ?>" tabindex="3" />
					<?php wp_nonce_field('add-' . $taxonomy, '_ajax_nonce-add-' . $taxonomy, false); ?>
					<span id="<?php echo $taxonomy; ?>-ajax-response"></span>
				</p>
			</div>
		<?php endif; ?>
		</div>
	<?php
	}
		
}

?>