<?php
App::uses('AppHelper', 'helpers');

class DataStorageHelper extends HtmlHelper {
    public function image($path, $options = array()) {
        //$token = base64_encode( Cache::read( 'Security.salt' ) . $path );
        return parent::image('images.php?k=' . base64_encode($path) , array_merge($options, array(
            'pathPrefix' => ''
        )));
    }

    public function imageToDataUrl($path, $options = array()) {
        if (is_file($path)) {
            $content = file_get_contents($path);
            $mimeType = mime_content_type($path);
            $url = 'data:' . $mimeType . ';base64,' . base64_encode($content);
            return '<img src="' . $url . '" />';
        } else {
            return '<img src="/img/missing_photo.png" />';
        }
    }
}
