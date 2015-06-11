<?php

function content_grid_append_to_content( $content ) {
	$rn = "\r\n"; // .chr(13).chr(10)
	$append_html = '';

	if( is_page() ) {

		$post_id = get_the_ID();
		$content_grid_data = get_post_meta( $post_id, '_content_grid_data', true );

		$append_html .= $rn.'<!-- powered by Content-grid plugin v.'.CONTENT_GRID_VERSION.' wordpress.org/plugins/content-grid/ -->'.$rn;

		$content_grid_schema = get_post_meta($post_id, '_content_grid_schema', true);
		
		if(!empty($content_grid_schema)) {
			foreach ($content_grid_schema as $row_key => $row_value): // each row
				$append_html .= '<div class="cg-row">'.$rn;
				foreach ($row_value as $col_key => $col_value): // each col
					$append_html .= '	<div class="cg-col-'.$col_value['col_type'].'">'.$rn;
					$content_html = do_shortcode($content_grid_data[$col_value['col_content_id']]);
					$append_html .= $content_html.$rn;
					$append_html .= '	</div><!-- .cg-col-'.$col_value['type'].' -->'.$rn;
				endforeach;
				$append_html .= '</div><!-- .cg-row -->'.$rn;
			endforeach;
			$append_html = '<div class="cg-wrap">'.$rn.$append_html.'</div><!-- .cg-wrap -->'.$rn;
		} else {
			$append_html .= '<!-- There no Content-grid data saved for this page. -->'.$rn;
		}

		$content .= $append_html;
	}

	return $content;
}
add_filter( 'the_content', 'content_grid_append_to_content' );


function content_grid_enqueue_assets() {
	if (is_page()) { // load assets only on pages
		wp_enqueue_style('content-grid-style', plugins_url('/css/content-grid.css', __FILE__), false, CONTENT_GRID_VERSION, 'all');
	}
}
add_action( 'wp_enqueue_scripts', 'content_grid_enqueue_assets' );