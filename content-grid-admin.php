<?php

function content_grid_add_meta_box() {

	$screens = array( 'page' ); // add meta boxes only for pages

	foreach ( $screens as $screen ) {

		add_meta_box(
			'content_grid_sectionid',
			'Content-Grid',
			'content_grid_meta_box_callback',
			$screen
		);
	}
}
add_action( 'add_meta_boxes', 'content_grid_add_meta_box' );


function content_grid_schema($post_id) {
	global $content_grid_settings;

	$html = '';

	$content_grid_schema = get_post_meta($post_id, '_content_grid_schema', true);

	$content_grid_row_options = [
		0 => 'empty',
		1 => '1 column (100%)',
		2 => '2 columns (50%/50%)'
	];

	$html .= '<div class="cg-admin-wrap">';

	for ($i_row = 1; $i_row <= $content_grid_settings['content_rows']; $i_row++): // run thru rows
		$cols_num = 0;
		if(isset($content_grid_schema[$i_row])) {
			$cols_num = count($content_grid_schema[$i_row]);
		}
		
		$html .= '<div class="cg-row cg-admin-row">';

			$html .= '<div class="cg-col-12">';
			$html .= '<select class="cg-row-control" name="content_grid_row_'.$i_row.'">';
			foreach ($content_grid_row_options as $option_key => $option_value):
				$selected = '';
				if( $cols_num == $option_key) {
					$selected = 'selected="selected"';
				}
				$html .= '<option value="'.$option_key.'" '.$selected.'>'.$option_value.'</option>';
			endforeach;
			$html .= '</select>';
			$html .= '<div class="cg-pull-right">row #'.$i_row.'</div>';
			$html .= '</div><!-- .cg-col-12 -->';

			$class_row = '';
			if($cols_num = 0) {
				$class_row = 'cg-hide';
			}

			$html .= '<div class="cg-row js-cg-admin-cols-wrap cg-clearfix '.$class_row.'">';

			for ($i_col = 1; $i_col <= 2; $i_col++): // run thru cols
				
				if(isset($content_grid_schema[$i_row][$i_col]['col_type'])) {
					$col_type = $content_grid_schema[$i_row][$i_col]['col_type'];
					$col_class = 'cg-col-'.$col_type;
				} else {
					$col_class = 'cg-hide';
				}
				
				$col_content_id = 0;
				if(isset($content_grid_schema[$i_row][$i_col]['col_type'])) {
					$col_content_id = $content_grid_schema[$i_row][$i_col]['col_content_id'];
				}
				
				$html .= '<div class="js-cg-admin-col '. $col_class .'">';
				$html .= '<div class="cg-admin-col-inner">';

				$html .= 'Content <select class="cg-col-control" name="content_grid_col_row_'.$i_row.'_col_'.$i_col.'">';
				$html .= '<option value="0">-</option>';
					for ($i_content = 2; $i_content <= $content_grid_settings['content_areas']; $i_content++):
						$selected = '';
						if( $i_content == $col_content_id) {
							$selected = 'selected="selected"';
						}
						$html .= '<option value="'.$i_content.'" '.$selected.'>#'.$i_content.'</option>';
					endfor;
				$html .= '</select>';

				$html .= '<div class="cg-pull-right">col #'.$i_col.'</div>';
				$html .= '</div><!-- .cg-admin-col-inner -->';
				$html .= '</div><!-- .cg-col-# -->';
			endfor;

			$html .= '</div><!-- .cg-row .js-cg-admin-cols-wrap -->';

		$html .= '</div><!-- .cg-row -->';

	endfor;

	$html .= '<p>Do not forget to click the Update button at the Publish section to save all your changes.</p>';

	$html .= '</div><!-- .cg-admin-wrap -->';

	return $html;
} // end of content_grid_schema()


function content_grid_meta_box_callback( $post ) {
	global $content_grid_settings;

	// Add an nonce field so we can check for it later.
	wp_nonce_field( 'content_grid_meta_box', 'content_grid_meta_box_nonce' );

	/*
	 * Use get_post_meta() to retrieve an existing value
	 * from the database and use the value for the form.
	 */

	$content_grid_data = get_post_meta( $post->ID, '_content_grid_data', true );

	echo '<h2 class="nav-tab-wrapper content-grid-nav-tab-wrapper">';

	echo '<a href="#" class="nav-tab content-grid-nav-tab content-grid-nav-tab-1">Grid</a>';

	for ($i = 2; $i <= $content_grid_settings['content_areas']; $i++): // print tabs
		echo '<a href="#" class="nav-tab content-grid-nav-tab content-grid-nav-tab-'.$i.'">#'.$i.'</a>';
	endfor;
	echo '</h2>';

	echo '<div class="content-grid-group-wrapper">';

	echo '	<section class="content-grid-group content-grid-group-1">';
	echo content_grid_schema($post->ID);
	echo '	</section><!-- .content-grid-group-1 -->';


	for ($i = 2; $i <= $content_grid_settings['content_areas']; $i++): // print tabs content
		echo '  <section class="content-grid-group content-grid-group-'.$i.'" style="display: none;">';
		echo '  <h2>Content #'.$i.'</h2>';
		$editor_options = array(
			'editor_height' => 400,
			'tinymce' => array(
				'autoresize_min_height' => 400,
				'wp_autoresize_on' => false
			)
		);
		wp_editor($content_grid_data[$i], 'content_grid_data_'.$i, $editor_options);
		echo '	</section><!-- .content-grid-group-'.$i.' -->';
	endfor;

	echo '</div><!-- .content-grid-group-wrapper -->';

}


function content_grid_save_meta_box_data( $post_id ) {
	global $content_grid_settings;

	/*
	 * We need to verify this came from our screen and with proper authorization,
	 * because the save_post action can be triggered at other times.
	 */

	// Check if our nonce is set.
	if ( ! isset( $_POST['content_grid_meta_box_nonce'] ) ) {
		return;
	}

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['content_grid_meta_box_nonce'], 'content_grid_meta_box' ) ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions.
	if ( isset( $_POST['post_type'] ) && $_POST['post_type'] == 'page' ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}
	} else {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	/* OK, it's safe for us to save the data now. */

	// Make sure that it is set.
	//if ( ! isset( $_POST['content_grid_data_2'] ) ) {
	//	return;
	//}

	// Sanitize user input.
	//$content_grid_data = sanitize_text_field( $_POST['content_grid_data'] );

	$content_grid_data_array = [];
	for ($i = 2; $i <= $content_grid_settings['content_areas']; $i++):
		$content_grid_data_array[$i] = $_POST['content_grid_data_'.$i];
	endfor;

	// Update the meta field in the database.
	//if( !empty( $_POST[ 'content_grid_data_2' ] ) ) {
		update_post_meta( $post_id, '_content_grid_data', $content_grid_data_array );
	//} else {
	//	delete_post_meta( $post_id, '_content_grid_data' ); // clean up meta
	//}

	/*
	making schema array like this:
	$content_grid_schema_sample = [
		1 => [ // row
			1 => [ // col
				'col_type' => 6, // col 50%
				'col_content_id' => 2 // content #2
			],
			2 => [ // col
				'col_type' => 6, // col 50%
				'col_content_id' => 3 // content #3
			]
		],
		2 => [ // row
			1 => [ // col
				'col_type' => 12, // col 100%
				'col_content_id' => 4 // content #4
			]
		]
	];
	*/

	$content_grid_schema_array = [];
	for ($i_row = 1; $i_row <= $content_grid_settings['content_rows']; $i_row++): // run thru rows
		$content_grid_schema_temp_array = [];
		$cols_in_row = intval($_POST['content_grid_row_'.$i_row]);
		if($cols_in_row != 0) {
			for ($i_col = 1; $i_col <= $cols_in_row; $i_col++): // run thru cols
				$col_type = 12; // 1 wide col, 100%
				if($cols_in_row == 2) { // 2 cols, 50%/50%
					$col_type = 6;
				}
				$content_grid_schema_temp_array[$i_col] = array(
					'col_type' => $col_type,
					'col_content_id' => $_POST['content_grid_col_row_'.$i_row.'_col_'.$i_col]
				);
			endfor;
		}
		$content_grid_schema_array[$i_row] = $content_grid_schema_temp_array;
	endfor;

	update_post_meta( $post_id, '_content_grid_schema', $content_grid_schema_array );
}
add_action( 'save_post', 'content_grid_save_meta_box_data' );


function content_grid_admin_enqueue_assets() {
	wp_enqueue_style( 'content-grid-admin-style', plugins_url( '/css/content-grid-admin.css', __FILE__ ), false, CONTENT_GRID_VERSION, 'all' );
	wp_enqueue_script( 'content-grid-admin-script', plugins_url( '/js/content-grid-admin.js', __FILE__ ), array('jquery'), CONTENT_GRID_VERSION );
}
add_action( 'admin_enqueue_scripts', 'content_grid_admin_enqueue_assets' );
