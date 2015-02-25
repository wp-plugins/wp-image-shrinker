<?php
/**
 * Plugin Name: WordPress Image Shrinker
 * Plugin URI: http://www.hetworks.nl
 * Description: Reduce image file sizes drastically and improve performance and Pagespeed score using the TinyPNG API within WordPress. Works for both PNG and JPG images.
 * Version: 1.1.0
 * Author: HETWORKS
 * Author URI: http://www.hetworks.nl
 * License: GPLv2 or later
 */

add_action('admin_menu', 'hetworkstinypng_createoptionspage');

function hetworkstinypng_createoptionspage() {
	add_menu_page('Image Shrinker', 'Image Shrinker', 'administrator', 'hetworkstinypng_optionspage', 'hetworkstinypng_displayoptionspage');
	add_action( 'admin_init', 'hetworkstinypng_registersettings' );
}

function hetworkstinypng_registersettings() {
	register_setting('hetworkstinypng_settings', 'tinypng_apikey');
	register_setting('hetworkstinypng_settings', 'hetworkstinypng_sizes');
	$sizearr = get_option('hetworkstinypng_sizes');
	foreach (get_intermediate_image_sizes() as $imagesize) {
		if (!isset($sizearr[$imagesize])){
			$sizearr[$imagesize] = true;
		}
	}
	update_option('hetworkstinypng_sizes', $sizearr);
}

/*
* Link naar settings page 
*/
function hetworkstinypng_add_settings_link( $links ) {
    $settings_link = '<a href="options-general.php?page=hetworkstinypng_optionspage">' . __( 'Settings' ) . '</a>';
    array_push( $links, $settings_link ); 
	if (get_option('tinypng_apikey') == '') {
		add_settings_error('tinypng_apikey', 'tinypng_apikey_missing_', 'Before you can use WP Image Shrinker make sure to enter a TinyPNG API Key in the <a href="options-general.php?page=hetworkstinypng_optionspage">Image Shrinker options page</a>', 'updated');
		settings_errors();
	}
  	return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'hetworkstinypng_add_settings_link' );

/*
* Options pagina
*/
function hetworkstinypng_displayoptionspage() { ?>

<div class="wrap">
	<h2>WordPress Image Shrinker by HETWORKS</h2>
	<p>In order for this plugin to work you need to enter an API key from TinyPNG. You can generate your key on <a href="https://tinypng.com/developers" target="_blank">https://tinypng.com/developers</a>.
	An API key is free for the first 500 images each month. If you have more images you
	need to pay a small amount.</p>
	<form method="post" action="options.php">
	<?php settings_fields( 'hetworkstinypng_settings' ); ?>
    <?php do_settings_sections( 'hetworkstinypng_settings' ); ?>
	<table class="form-table">
		<tr valign="top">
			<th scope="row">Your TinyPNG API Key</th>
			<td><input type="text" name="tinypng_apikey" style="min-width: 300px;" value="<?php echo esc_attr( get_option('tinypng_apikey') ); ?>" /></td>
		</tr>
	</table>
	<h4>Image sizes to compress</h4>
	<p>Which sizes do you want to compress when you click on the "Shrink all current images" button? (on a new upload the full image is compressed before the other sizes are made by wordpress so only 1 api call is needed on new upload)</p>
	<?php $sizearr = get_option('hetworkstinypng_sizes'); ?>
	<table>
		<tr>
			<td><input class="hetworkstinypng_size disabled" type="checkbox" id="hetworkstinypng_sizes_full" name="" value="1" checked disabled /></td>
			<td>full</td>
		</tr>
		<?php foreach (get_intermediate_image_sizes() as $imagesize) { ?>
			<tr>
				<td>
					<input type="hidden" name="hetworkstinypng_sizes[<?= $imagesize; ?>]" value="0" />
					<input class="hetworkstinypng_size" type="checkbox" id="hetworkstinypng_sizes_<?= $imagesize; ?>" name="hetworkstinypng_sizes[<?= $imagesize; ?>]" value="1" <?php if ($sizearr[$imagesize] == true) { echo 'checked'; } ?> />
				</td>
				<td><label for="hetworkstinypng_sizes_<?= $imagesize; ?>"><?= $imagesize; ?></label></td>
			</tr>
		<?php } ?>
	</table>
	<?php submit_button(); ?>
</form>
	<p>With the button below you can reprocess all the images in your Media library. You will see a great improvement in your Google Pagespeed score afterwards.</p>
	<p class="hetworkstinypng_sizes_error" style="display: none; color:red;">Save changes to settings before Shrinking current images</p>
	<?php if (get_option('tinypng_apikey') != '') { $bclasses = 'secondary hetworkstinypng_ajax_button'; } else { $bclasses = 'secondary disabled'; } ?>
	<?php submit_button('Shrink all current images', $bclasses); ?> <div class="bespaarddiv">Space saved: <span class="bespaard"></span> (<span class="ratio"></span>%)</div>
	<table class="hetworkstinypng_images" style="display: none;">
		<thead>
			<tr>
				<th>Image</th><th>Image name</th>
					<th>full</th>
					<?php foreach (get_intermediate_image_sizes() as $imagesize) { ?>
						<?php if ($sizearr[$imagesize] == true) { ?>
							<th><?= $imagesize; ?></th>
						<?php }?>
					<?php } ?>
			</tr>
		</thead>
		<tbody>
		
		</tbody>
	</table>
	<p>Do you see the great advantage of this plugin? Please donate a small amount: <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
		<input type="hidden" name="cmd" value="_s-xclick">
		<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
		">
		<input type="image" src="https://www.paypalobjects.com/nl_NL/NL/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal, de veilige en complete manier van online betalen.">
		<img alt="" border="0" src="https://www.paypalobjects.com/nl_NL/i/scr/pixel.gif" width="1" height="1">
		</form>
	</p>
</div>
<?php
}

add_action( 'admin_footer', 'hetworkstinypng_ajaxactionrequest' );

function hetworkstinypng_ajaxactionrequest() { ?>
<?php $sizearr = get_option('hetworkstinypng_sizes'); ?>
	<script type="text/javascript" >
	var voor = 0;
	var na = 0;
	var bespaard = 0;
	jQuery(document).ready(function($) {
		$(".hetworkstinypng_size").change(function() {
			$(".hetworkstinypng_ajax_button").addClass('disabled');
			$(".hetworkstinypng_sizes_error").show();
		});
		$(".hetworkstinypng_ajax_button").click(function() {
			if (!$(this).hasClass('disabled')) {
				$(this).addClass('disabled');
				$(".bespaarddiv").show().find('span.bespaard').html(bytesToSize(bespaard));
				var data = {
					'action': 'hetworkstinypng_ajaxaction_getimages'
				};

				$.post(ajaxurl, data, function(response) {
					$("table.hetworkstinypng_images tbody").html('').parent('table').show();
					$.each($.parseJSON(response), function(i,v) {
						if (v.status != 'done') {
							$("table.hetworkstinypng_images tbody").append('<tr data-id="'+v.id+'" data-basename="'+v.basename+'" data-url="'+v.url+'" data-done="false" data-sizes=\''+v.sizes+'\'><td><img class="hetworkstinypng_thumb" src="'+v.thumb+'" /></td><td>'+v.basename+'</td><td class="sizetd" data-tdsize="full"></td><?php foreach (get_intermediate_image_sizes() as $imagesize) { ?><?php if ($sizearr[$imagesize] == true) { ?><td class="sizetd" data-tdsize="<?= $imagesize; ?>"></td><?php }} ?></tr>');
						} else {
							$("table.hetworkstinypng_images tbody").append('<tr data-id="'+v.id+'" data-basename="'+v.basename+'" data-url="'+v.url+'" data-done="true" data-sizes=\''+v.sizes+'\'><td><img class="hetworkstinypng_thumb" src="'+v.thumb+'" /></td><td>'+v.basename+'</td><td class="sizetd" data-tdsize="full"></td><?php foreach (get_intermediate_image_sizes() as $imagesize) { ?><?php if ($sizearr[$imagesize] == true) { ?><td class="sizetd" data-tdsize="<?= $imagesize; ?>"></td><?php }} ?></tr>');
						}
					});
					$(".sizetd").html('<span class="dashicons dashicons-clock"></span>');
					$("tr[data-done='true'] .sizetd").html('<span class="dashicons dashicons-yes"></span>');
					hetworkstinypng_ajax_sendnextimage();
					
				});
			}
		});
	});
	
	function hetworkstinypng_ajax_sendnextimage() {
		var nextimage = jQuery("table.hetworkstinypng_images tbody tr[data-done='false']").first();
		if (nextimage.length != 0) {
			nextimage.attr('data-done', 'true');
			var id = nextimage.attr('data-id');
			sizes = jQuery.parseJSON(nextimage.attr('data-sizes'));
			jQuery.each(sizes, function(size,url) {
				var tdata = {
					'action': 'hetworkstinypng_ajaxaction_tinypngimage',
					'url': url,
					'id': id,
					'dataType': 'json'
				};
			jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: tdata,
					beforeSend: function() {
						nextimage.find("td[data-tdsize='"+size+"']").html('').append('<img src="<?php echo plugins_url( 'img/ajax-loader.gif', __FILE__ ); ?>"  />');
					}, 
					success: completeHandler = function(response) {
						try {
							res = jQuery.parseJSON(response);
						} catch(error) {
							console.log(error);
						}						
						if (typeof res !== 'undefined' && typeof res.error === 'undefined' && typeof error === 'undefined') {
							nextimage.find("td[data-tdsize='"+size+"']").html('').append('<table class="intable"><tr><td>From:</td><td>' + bytesToSize(res.input.size) + '</td></tr><tr><td colspan="2"><span class="dashicons dashicons-yes"></span></td></tr><tr><td>To:</td><td>' + bytesToSize(res.output.size) + '</td></tr></table>'); 
							voor =  voor + res.input.size;
							na = na + res.output.size;
							jQuery("span.ratio").html((100 - ((na / voor) * 100)).toFixed(2));
							bespaard = bespaard + (res.input.size - res.output.size);
							jQuery("span.bespaard").html(bytesToSize(bespaard));
							if (size == 'full') {
								hetworkstinypng_ajax_sendnextimage();
								var donedata = {
									'action': 'hetworkstinypng_ajaxaction_tinypngdone',
									'id': id
								};
								jQuery.ajax({
									type: 'POST',
									url: ajaxurl,
									data: donedata,
									success: completeHandler = function(response) {
										//console.log(response);
									}
								});
							}
						} else if (typeof res !== 'undefined' && typeof res.error !== 'undefined') {
							nextimage.find("td[data-tdsize='"+size+"']").html('').append('<span class="dashicons dashicons-no-alt"></span><br />' + res.message);
							if (res.error == 'TooManyRequests') {
								jQuery("div.bespaarddiv").css('color', 'red').html(res.message);
							} else {
								console.log(res.error);
								if (size == 'full') {
									hetworkstinypng_ajax_sendnextimage();
								}
							}
						} else if (typeof res === 'undefined') {							
							nextimage.find("td[data-tdsize='"+size+"']").html('').append('<span class="dashicons dashicons-no-alt"></span>');
							if (size == 'full') {
								hetworkstinypng_ajax_sendnextimage();
							}
						}
					}, 
					error: errorHandler = function(data) {
						console.log('error');
					}
				});
			});
		} else {
			console.log('done');
		}
	}

	function bytesToSize(bytes) {
	   if(bytes == 0) return '0 Byte';
	   var k = 1000;
	   var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
	   var i = Math.floor(Math.log(bytes) / Math.log(k));
	   return (bytes / Math.pow(k, i)).toPrecision(3) + ' ' + sizes[i];
	}
	</script>
	<style>
	table.hetworkstinypng_images { border-spacing: 0; border: 1px solid grey; }
	table.hetworkstinypng_images td, table.hetworkstinypng_images th {padding: 16px; }
	table.hetworkstinypng_images td img.hetworkstinypng_thumb { height: 32px; }
	table.intable { margin: 0 auto; }
	table.intable td { padding: 2px; height: auto; }
	table.hetworkstinypng_images td.sizetd { text-align: center; width: 130px; padding: 0; border-top: 1px solid grey; border-left: 1px solid grey; }
	.bespaarddiv { display: none; margin-bottom: 20px;font-size: 16px;font-weight: bold; }
	</style> <?php
}

add_action( 'wp_ajax_hetworkstinypng_ajaxaction_getimages', 'hetworkstinypng_ajaxaction_getimages' );
add_action( 'wp_ajax_hetworkstinypng_ajaxaction_tinypngimage', 'hetworkstinypng_ajaxaction_tinypngimage' );
add_action( 'wp_ajax_hetworkstinypng_ajaxaction_tinypngdone', 'hetworkstinypng_ajaxaction_tinypngdone' );

function hetworkstinypng_ajaxaction_getimages() {
	$response = '';	
	$query_images_args = array(
		'post_type' => 'attachment', 'post_mime_type' =>'image', 'post_status' => 'inherit', 'posts_per_page' => -1, 'meta_compare' => 'NOT EXISTS',
	);

	$query_images = new WP_Query( $query_images_args );
	$images = Array();
	$i = 0;
	foreach ( $query_images->posts as $image) {
		$images[$i]['url'] = wp_get_attachment_url( $image->ID );
		$thumb = wp_get_attachment_image_src( $image->ID, 'thumbnail' );
		$images[$i]['thumb'] = $thumb[0];
		$images[$i]['basename'] = basename($images[$i]['url']);
		$images[$i]['id'] = $image->ID;
		$images[$i]['sizes'] = Array();
		$sizearr = get_option('hetworkstinypng_sizes');
		foreach (get_intermediate_image_sizes() as $imagesize) {
			if ($sizearr[$imagesize] == true) {
				$size = wp_get_attachment_image_src( $image->ID, $imagesize);
				$images[$i]['sizes'][$imagesize] = $size[0];
			}
		}
		$images[$i]['sizes']['full'] = $images[$i]['url'];
		$images[$i]['sizes'] = json_encode($images[$i]['sizes']);
		$images[$i]['status'] = get_post_meta($image->ID, 'hetworkstinypng_status', true);
		$i++;
	}
	
	echo json_encode($images);
	wp_die(); // Nodig voor propere ajax response
}

function hetworkstinypng_ajaxaction_tinypngimage() {
	$key = get_option('tinypng_apikey');
	$url = $_POST['url'];
	$input = $url;
	$output = '../../'. parse_url($url, PHP_URL_PATH);
	$response = hetworkstinypng_tinypngrequest($input, $output, $key);
	echo $response;
	wp_die(); // Nodig voor propere ajax response
}

function hetworkstinypng_ajaxaction_tinypngdone() {
	$id = $_POST['id'];
	echo update_post_meta($id, 'hetworkstinypng_status', 'done');
	wp_die(); // Nodig voor propere ajax response
}

/*
* Na upload, voor wp_handle_upload, image door TinyPNG gooien
*/
add_filter('wp_handle_upload_prefilter', 'hetworkstinypng_uploadevent' );
add_filter('wp_generate_attachment_metadata', 'hetworkstinypng_metadata', 10, 2 );

function hetworkstinypng_uploadevent( $file ){
    
	if (get_option('tinypng_apikey') != '') {
		if ($file['type'] == 'image/jpeg' OR $file['type'] == 'image/png') {
			$key = get_option('tinypng_apikey');
			$input = $file['tmp_name'];		
			$output = $file['tmp_name'];
			hetworkstinypng_tinypngrequest($input, $output, $key);
		}
	}
    return $file;
}

function hetworkstinypng_metadata($metadata, $attachment_id) {
	if (get_option('tinypng_apikey') != '') {
		update_post_meta($attachment_id, 'hetworkstinypng_status', 'done');
	}
	return $metadata;
}

/*
* de request naar TinyPNG toe
*/
function hetworkstinypng_tinypngrequest($input, $output, $key) {
		$request = curl_init();
		curl_setopt_array($request, array(
		  CURLOPT_URL => "https://api.tinypng.com/shrink",
		  CURLOPT_USERPWD => "api:" . $key,
		  CURLOPT_POSTFIELDS => file_get_contents($input),
		  CURLOPT_BINARYTRANSFER => true,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_HEADER => true,
		  CURLOPT_CAINFO => dirname(__FILE__) . '/cacert.pem',
		  CURLOPT_SSL_VERIFYPEER => true 
		));

		$response = curl_exec($request);
		$json = explode("\r\n\r\n", $response);
		$json = $json[2];
		$jsonarr = json_decode($json);
		if (curl_getinfo($request, CURLINFO_HTTP_CODE) === 201) {

		  $headers = substr($response, 0, curl_getinfo($request, CURLINFO_HEADER_SIZE));
		  foreach (explode("\r\n", $headers) as $header) {
			if (substr($header, 0, 10) === "Location: ") {
			  $request = curl_init();
			  curl_setopt_array($request, array(
				CURLOPT_URL => substr($header, 10),
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CAINFO => dirname(__FILE__) . '/cacert.pem',
				CURLOPT_SSL_VERIFYPEER => true
			  ));
			  file_put_contents($output, curl_exec($request));
			  return $json;
			}
		  }
		} else {
			if ($jsonarr->error == 'TooManyRequests') {
				return $json;
			}
			$jerror = Array();
			$jerror['error'][] = curl_error($request);
			$jerror['error'][] = $json;
			return $jerror;
		}
}