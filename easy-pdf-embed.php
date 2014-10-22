<?php

/*
** Plugin Name: Easy PDF Embed (HTML5 w/ Java fallback)
** Plugin URI: https://wordpress.org/plugins/easy-pdf-embed/
** Description: Embed PDFs and view with PDF.js or EmbedPDF (Java) via the Add Media button
** Version: 1.0
** Author: Hatsize
** Author URI: http://hatsize.com
** License: GPLv2
*/

// Plugin Assets
wp_enqueue_script( 'easy-pdf-embed', plugins_url('js/easy-pdf-embed.js', __FILE__ ), false, '1.0' );
wp_enqueue_style( 'easy-pdf-embed', plugins_url('css/easy-pdf-embed-post.css', __FILE__ ), false, '1.0' );

// Add the TinyMCE NonEditable Plugin
add_filter('mce_external_plugins', 'easy_pdf_embed_mce_external_plugins');
function easy_pdf_embed_mce_external_plugins () {
     $plugin = 'noneditable';
     $plugins_array = array($plugin => plugins_url('lib/tinymce/', __FILE__) . $plugin . '/plugin.min.js');
     return $plugins_array;
}

// Add easy-pdf Themes for TinyMCE
add_filter( 'mce_css', 'easy_pdf_embed_mce_css' );
function easy_pdf_embed_mce_css( $mce_css ) {
  if ( ! empty( $mce_css ) )
    $mce_css .= ',';

  $mce_css .= plugins_url('css/easy-pdf-embed-editor.css', __FILE__ );

  return $mce_css;
}

// Add PDFs from Media Library as easy-pdf embeds
add_filter ( 'media_send_to_editor', 'easy_pdf_embed_media_send_to_editor', 20, 2);
function easy_pdf_embed_media_send_to_editor($html, $id){

  $attachment = get_post($id);
  $out = $html;
  if ($attachment->post_mime_type == 'application/pdf') {
    $out = '<div class="mceNonEditable wp-easy-pdf-embed">[easy-pdf-embed filename="'.$attachment->guid.'"]</div>';
  }

  return $out;
}

// Add shortcode hook
add_shortcode('easy-pdf-embed', 'easy_pdf_embed_shortcode');
function easy_pdf_embed_shortcode($attributes) {
   $attributes = shortcode_atts(array(
                    'filename' => 'nofile.pdf'
                  ), $attributes, 'easy_pdf_embed');
   $pdfjs = plugins_url('lib/pdfjs/web/viewer.html', __FILE__ ).'?file='.$attributes['filename'];
   $embedpdf = plugins_url('lib/EmbedPDF/fontsseparate/EmbedPDF.jar', __FILE__ );
   $out  = '<script class="wp-easy-pdf-embed-pdfjs" type="text/html">';
   $out .= '<iframe width="100%" height="800px" src="'.$pdfjs.'"></iframe>';
   $out .= '</script>';
   $out .= '<script class="wp-easy-pdf-embed-embedpdf" type="text/html">';
   $out .= '<applet code="EmbedPDF.class" codebase="http://portal.dev" archive="'.$embedpdf.'" width="100%" height="800">
              <param name="pdf" value="'.$attributes['filename'].'"/>
              <param name="fonts" value="fontsseparate"/>
              <param name="enableOpenWindow" value="false"/>
              <param name="enableSubpixAA" value="true"/>
              <param name="enablePrinting" value="false"/>
              <param name="codebase_lookup" value="false"/>
              <param name="classloader_cache" value="false"/>
              <param name="java_arguments" value="-Djnlp.packEnabled=true -Xmx128m"/>
              <param name="image" value="splash.gif"/>
              <param name="boxborder" value="false"/>
              <param name="centerimage" value="true"/>
              <param name="permissions" value="sandbox"/>
            </applet>';
   $out .= '</script>';
   return $out;
}