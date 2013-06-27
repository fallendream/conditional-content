<?php
/*
 Plugin Name: Conditional Content
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: Displays content in a widged based on a ref variable appended to the URL.
Version: 0.1.0-SNAPSHOT
Author: Hunsrückvideo
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/

class cc_widget extends WP_Widget {

	public function __construct() {
		/**
		 * Übersetzungsfunktion für das Widget aktivieren.
		 * Die Sprachdateien liegen im Ordner "l10n" innerhalb des Widgets.
		 */
		if(function_exists('load_plugin_textdomain')) {
			load_plugin_textdomain($this->var_sTextdomain, PLUGINDIR . '/' . dirname(plugin_basename(__FILE__)) . '/l10n', dirname(plugin_basename(__FILE__)) . '/l10n');
		}

		parent::__construct(
				'cc_widget', // Base ID
				'Conditional Content', // Name
				array( 'description' => __( 'Loads page content inside the widget depending on the ref variable.', 'cc-widget')) // Args
		);

	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form($instance) {
		// Standardwerte
		$default = array('ref' => array(''), 'page' => array(''), 'default-page' => '');
		$instance = wp_parse_args((array) $instance, $default);

		wp_enqueue_script('jquery');
		wp_enqueue_script('cc-widget', plugins_url( 'cc-widget.js', __FILE__ ), array('jquery'));

		?>

<div>
	<p>
		<label for="<?php echo $this->get_field_id('default-page'); ?>"><?php _e('Default Page:','cc-widget'); ?><br>
			<input class="widefat"
			id="<?php echo $this->get_field_id('default-page'); ?>"
			name="<?php echo $this->get_field_name('default-page');?>"
			type="text" value="<?php echo $instance['default-page']; ?>"></label>
	</p>
</div>
<hr>

<div class="ccwidget-rules">
	<?php for($i = 0; $i < count($instance['ref']); $i++) { ?>
	<div class="ccwidget-rule">
		<p>
			<label for="<?php echo $this->get_field_id('ref'); ?>"><?php _e('Ref Tag:','cc-widget'); ?><br>
				<input class="widefat"
				id="<?php echo $this->get_field_id('ref'); ?>"
				name="<?php echo $this->get_field_name('ref');?>[]" type="text"
				value="<?php echo $instance['ref'][$i]; ?>"> </label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('page'); ?>"><?php _e('Page Adress:','cc-widget'); ?><br>
				<input class="widefat"
				id="<?php echo $this->get_field_id('page'); ?>"
				name="<?php echo $this->get_field_name('page');?>[]" type="text"
				value="<?php echo $instance['page'][$i]; ?>"> </label>
		</p>
		<div class="ccwidget-remove-button button button-primary">Delete</div>
		<hr>
	</div>
	<?php } ?>
</div>

<p>
<div class="ccwidget-add-button button button-primary">Add Rule</div>
</p>
<?php
	}

	/**
	 * Einstellungen, welche über das Formular kommen auf ihre Richtigkeit hin prüfen.
	 * Zwei ref Tags dürfen nicht identisch sein.
	 *
	 * @var array
	 */
	public function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$checked = array();
		for($i = 0; $i < count($new_instance['ref']); $i++) {
			if(!in_array($new_instance['ref'][$i], $checked)) {
				$checked[] = $new_instance['ref'][$i];
			} else {
				// Remove the doubled entry from the array.
				array_splice($new_instance['page'], $i, 1);
				array_splice($new_instance['ref'], $i, 1);
			}
		}
		$instance['page'] = $new_instance['page'];
		$instance['ref'] = $new_instance['ref'];
		$instance['default-page'] = $new_instance['default-page'];
		return $instance;
	}

	public function widget($args, $instance) {
		extract($args);

		$ref = $_GET['ref'];
		$key = array_search($ref, $instance['ref']);
		if($key === false) {
			// No key found.
			// Load default page if specified otherwise load nothing.
			if(!empty($instance['default-page'])) {
				$this->showPost($instance['default-page']);
				return;
			} else {
				return;
			}
		}
		$this->showPost($instance['page'][$key]);
	}

	/**
	 * Loads the designated page content and displays it.
	 * @param string $path
	 */
	private function showPost($path) {
		$post = get_page_by_path($path);
		$content = apply_filters('the_content', $post->post_content);
		echo $content;
	}
}



add_action('widgets_init', 'register_conditional_content_widget');
function register_conditional_content_widget() {
	register_widget('cc_widget');
}
?>