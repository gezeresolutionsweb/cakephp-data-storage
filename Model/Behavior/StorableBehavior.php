<?php
/**
 * Storable behavior handles automated data file upload.
 *
 * This CakePHP 2.X component is base on the work of Dave Baker.
 * @see https://gist.github.com/fullybaked/1986510
 *
 * @author Sylvain Lévesque
 */

App::uses('ModelBehavior', 'Model');

class StorableBehavior extends ModelBehavior {
    // constants for error types
    const SUCCESS = 0;
    const FILESIZE_EXCEED_SERVER_MAX = 1;
    const FILESIZE_EXCEED_FORM_MAX = 2;
    const PARTIAL_UPLOAD = 3;
    const NO_FILE_UPLOAD = 4;
    const NO_DIRECTORY_FOR_UPLOAD = 6;
    const SERVER_WRITE_FAIL = 7;
    const FILESIZE_EXCEEDS_CODE_MAX = 98;
    const FILE_FORMAT_NOT_ALLOWED = 99;
    const DESTINATION_NOT_AVAILABLE = 100;

    // Available file types
    public $fileTypes = array(
        '3dm' => 'x-world/x-3dmf',
        '3dmf' => 'x-world/x-3dmf',
        'a' => 'application/octet-stream',
        'aab' => 'application/x-authorware-bin',
        'aam' => 'application/x-authorware-map',
        'aas' => 'application/x-authorware-seg',
        'abc' => 'text/vnd.abc',
        'acgi' => 'text/html',
        'afl' => 'video/animaflex',
        'ai' => 'application/postscript',
        'aif' => array(
            'audio/aiff',
            'audio/x-aiff'
        ),
        'aifc' => array(
            'audio/aiff',
            'audio/x-aiff'
        ),
        'aiff' => array(
            'audio/aiff',
            'audio/x-aiff'
        ),
        'aim' => 'application/x-aim',
        'aip' => 'text/x-audiosoft-intra',
        'ani' => 'application/x-navi-animation',
        'aos' => 'application/x-nokia-9000-communicator-add-on-software',
        'aps' => 'application/mime',
        'arc' => 'application/octet-stream',
        'arj' => array(
            'application/arj',
            'application/octet-stream'
        ),
        'art' => 'image/x-jg',
        'asf' => 'video/x-ms-asf',
        'asm' => 'text/x-asm',
        'asp' => 'text/asp',
        'asx' => array(
            'application/x-mplayer2',
            'video/x-ms-asf',
            'video/x-ms-asf-plugin',
        ),
        'au' => array(
            'audio/basic',
            'audio/x-au'
        ),
        'avi' => array(
            'application/x-troff-msvideo',
            'video/avi',
            'video/msvideo',
            'video/x-msvideo'
        ),
        'avs' => 'video/avs-video',
        'bcpio' => 'application/x-bcpio',
        'bin' => array(
            'application/mac-binary',
            'application/macbinary',
            'application/octet-stream',
            'application/x-binary',
            'application/x-macbinary'
        ),
        'bm' => 'image/bmp',
        'bmp' => array(
            'image/bmp',
            'image/x-windows-bmp'
        ),
        'boo' => 'application/book',
        'book' => 'application/book',
        'boz' => 'application/x-bzip2',
        'bsh' => 'application/x-bsh',
        'bz' => 'application/x-bzip',
        'bz2' => 'application/x-bzip2',
        'c' => array(
            'text/plain',
            'text/x-c'
        ),
        'c++' => 'text/plain',
        'cat' => 'application/vnd.ms-pki.seccat',
        'cc' => array(
            'text/plain',
            'text/x-c'
        ),
        'ccad' => 'application/clariscad',
        'cco' => 'application/x-cocoa',
        'cdf' => array(
            'application/cdf',
            'cdf' => 'application/x-cdf',
            'cdf' => 'application/x-netcdf'
        ),
        'cer' => array(
            'application/pkix-cert',
            'application/x-x509-ca-cert'
        ),
        'cha' => 'application/x-chat',
        'chat' => 'application/x-chat',
        'class' => array(
            'application/java',
            'application/java-byte-code',
            'application/x-java-class'
        ),
        'com' => array(
            'application/octet-stream',
            'text/plain'
        ),
        'conf' => 'text/plain',
        'cpio' => 'application/x-cpio',
        'cpp' => 'text/x-c',
        'cpt' => array(
            'application/mac-compactpro',
            'application/x-compactpro',
            'application/x-cpt'
        ),
        'crl' => array(
            'application/pkcs-crl',
            'application/pkix-crl',
            'application/pkix-cert',
            'application/x-x509-ca-cert',
            'application/x-x509-user-cert'
        ),
        'csh' => array(
            'application/x-csh',
            'text/x-script.csh'
        ),
        'css' => array(
            'application/x-pointplus',
            'text/css'
        ),
        'cxx' => 'text/plain',
        'dcr' => 'application/x-director',
        'deepv' => 'application/x-deepv',
        'def' => 'text/plain',
        'der' => 'application/x-x509-ca-cert',
        'dif' => 'video/x-dv',
        'dir' => 'application/x-director',
        'dl' => array(
            'video/dl',
            'video/x-dl'
        ),
        'doc' => 'application/msword',
        'docm' => 'application/vnd.ms-word.document.macroEnabled.12',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'dot' => 'application/msword',
        'dotm' => 'application/vnd.ms-word.template.macroEnabled.12',
        'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
        'dp' => 'application/commonground',
        'drw' => 'application/drafting',
        'dump' => 'application/octet-stream',
        'dv' => 'video/x-dv',
        'dvi' => 'application/x-dvi',
        'dwf' => array(
            'drawing/x-dwf (old)',
            'model/vnd.dwf'
        ),
        'dwg' => array(
            'application/acad',
            'image/vnd.dwg',
            'image/x-dwg'
        ),
        'dxf' => array(
            'application/dxf',
            'image/vnd.dwg',
            'image/x-dwg'
        ),
        'dxr' => 'application/x-director',
        'el' => 'text/x-script.elisp',
        'elc' => array(
            'application/x-bytecode.elisp (compiled elisp)',
            'elc' => 'application/x-elc'
        ),
        'env' => 'application/x-envoy',
        'eps' => 'application/postscript',
        'es' => 'application/x-esrehber',
        'etx' => 'text/x-setext',
        'evy' => array(
            'application/envoy',
            'application/x-envoy'
        ),
        'exe' => 'application/octet-stream',
        'f' => array(
            'text/plain',
            'text/x-fortran'
        ),
        'f77' => 'text/x-fortran',
        'f90' => array(
            'text/plain',
            'f90' => 'text/x-fortran'
        ),
        'fdf' => 'application/vnd.fdf',
        'fif' => array(
            'application/fractals',
            'image/fif'
        ),
        'fli' => array(
            'video/fli',
            'video/x-fli'
        ),
        'flo' => 'image/florian',
        'flx' => 'text/vnd.fmi.flexstor',
        'fmf' => 'video/x-atomic3d-feature',
        'for' => array(
            'text/plain',
            'for' => 'text/x-fortran'
        ),
        'fpx' => array(
            'image/vnd.fpx',
            'image/vnd.net-fpx'
        ),
        'frl' => 'application/freeloader',
        'funk' => 'audio/make',
        'g' => 'text/plain',
        'g3' => 'image/g3fax',
        'gif' => 'image/gif',
        'gl' => array(
            'video/gl',
            'video/x-gl'
        ),
        'gsd' => 'audio/x-gsm',
        'gsm' => 'audio/x-gsm',
        'gsp' => 'application/x-gsp',
        'gss' => 'application/x-gss',
        'gtar' => 'application/x-gtar',
        'gz' => array(
            'application/x-compressed',
            'application/x-gzip'
        ),
        'gzip' => array(
            'application/x-gzip',
            'gzip' => 'multipart/x-gzip'
        ),
        'h' => array(
            'text/plain',
            'h' => 'text/x-h'
        ),
        'hdf' => 'application/x-hdf',
        'help' => 'application/x-helpfile',
        'hgl' => 'application/vnd.hp-hpgl',
        'hh' => array(
            'text/plain',
            'text/x-h'
        ),
        'hlb' => 'text/x-script',
        'hlp' => array(
            'application/hlp',
            'application/x-helpfile',
            'application/x-winhelp'
        ),
        'hpg' => 'application/vnd.hp-hpgl',
        'hpgl' => 'application/vnd.hp-hpgl',
        'hqx' => array(
            'application/binhex',
            'application/binhex4',
            'application/mac-binhex',
            'application/mac-binhex40',
            'application/x-binhex40',
            'application/x-mac-binhex40'
        ),
        'hta' => 'application/hta',
        'htc' => 'text/x-component',
        'htm' => 'text/html',
        'html' => 'text/html',
        'htmls' => 'text/html',
        'htt' => 'text/webviewhtml',
        'htx' => 'text/html',
        'ice' => 'x-conference/x-cooltalk',
        'ico' => 'image/x-icon',
        'idc' => 'text/plain',
        'ief' => 'image/ief',
        'iefs' => 'image/ief',
        'iges' => array(
            'application/iges',
            'model/iges'
        ),
        'igs' => array(
            'application/iges',
            'model/iges'
        ),
        'ima' => 'application/x-ima',
        'imap' => 'application/x-httpd-imap',
        'inf' => 'application/inf',
        'ins' => 'application/x-internett-signup',
        'ip' => 'application/x-ip2',
        'isu' => 'video/x-isvideo',
        'it' => 'audio/it',
        'iv' => 'application/x-inventor',
        'ivr' => 'i-world/i-vrml',
        'ivy' => 'application/x-livescreen',
        'jam' => 'audio/x-jam',
        'jav' => array(
            'text/plain',
            'text/x-java-source'
        ),
        'java' => array(
            'text/plain',
            'text/x-java-source'
        ),
        'jcm' => 'application/x-java-commerce',
        'jfif' => array(
            'image/jpeg',
            'image/pjpeg'
        ),
        'jfif-tbnl' => 'image/jpeg',
        'jpe' => array(
            'image/jpeg',
            'jpe' => 'image/pjpeg'
        ),
        'jpeg' => array(
            'image/jpeg',
            'image/pjpeg'
        ),
        'jpg' => array(
            'image/jpeg',
            'image/pjpeg'
        ),
        'jps' => 'image/x-jps',
        'js' => array(
            'application/x-javascript',
            'application/javascript',
            'application/ecmascript',
            'text/javascript',
            'text/ecmascript'
        ),
        'jut' => 'image/jutvision',
        'kar' => array(
            'audio/midi',
            'music/x-karaoke'
        ),
        'ksh' => array(
            'application/x-ksh',
            'text/x-script.ksh'
        ),
        'la' => array(
            'audio/nspaudio',
            'audio/x-nspaudio'
        ),
        'lam' => 'audio/x-liveaudio',
        'latex' => 'application/x-latex',
        'lha' => array(
            'application/lha',
            'application/octet-stream',
            'application/x-lha'
        ),
        'lhx' => 'application/octet-stream',
        'list' => 'text/plain',
        'lma' => array(
            'audio/nspaudio',
            'audio/x-nspaudio'
        ),
        'log' => 'text/plain',
        'lsp' => array(
            'application/x-lisp',
            'text/x-script.lisp'
        ),
        'lst' => 'text/plain',
        'lsx' => 'text/x-la-asf',
        'ltx' => 'application/x-latex',
        'lzh' => array(
            'application/octet-stream',
            'application/x-lzh',
            'application/lzx',
            'application/octet-stream',
            'application/x-lzx'
        ),
        'm' => array(
            'text/plain',
            'text/x-m',
        ),
        'm1v' => 'video/mpeg',
        'm2a' => 'audio/mpeg',
        'm2v' => 'video/mpeg',
        'm3u' => 'audio/x-mpequrl',
        'man' => 'application/x-troff-man',
        'map' => 'application/x-navimap',
        'mar' => 'text/plain',
        'mbd' => 'application/mbedlet',
        'mc$' => 'application/x-magic-cap-package-1.0',
        'mcd' => array(
            'application/mcad',
            'application/x-mathcad'
        ),
        'mcf' => array(
            'image/vasa',
            'text/mcf'
        ),
        'mcp' => 'application/netmc',
        'me' => 'application/x-troff-me',
        'mht' => 'message/rfc822',
        'mhtml' => 'message/rfc822',
        'mid' => array(
            'application/x-midi',
            'audio/midi',
            'audio/x-mid',
            'audio/x-midi',
            'music/crescendo',
            'x-music/x-midi'
        ),
        'midi' => array(
            'application/x-midi',
            'audio/midi',
            'audio/x-mid',
            'audio/x-midi',
            'music/crescendo',
            'x-music/x-midi'
        ),
        'mif' => array(
            'application/x-frame',
            'application/x-mif'
        ),
        'mime' => array(
            'message/rfc822',
            'www/mime'
        ),
        'mjf' => 'audio/x-vnd.audioexplosion.mjuicemediafile',
        'mjpg' => 'video/x-motion-jpeg',
        'mm' => array(
            'application/base64',
            'application/x-meme'
        ),
        'mme' => 'application/base64',
        'mod' => array(
            'audio/mod',
            'audio/x-mod'
        ),
        'moov' => 'video/quicktime',
        'mov' => 'video/quicktime',
        'movie' => 'video/x-sgi-movie',
        'mp2' => array(
            'audio/mpeg',
            'audio/x-mpeg',
            'video/mpeg',
            'video/x-mpeg',
            'video/x-mpeq2a'
        ),
        'mp3' => array(
            'audio/mpeg3',
            'audio/x-mpeg-3',
            'video/mpeg',
            'video/x-mpeg'
        ),
        'mpa' => array(
            'audio/mpeg',
            'video/mpeg'
        ),
        'mpc' => 'application/x-project',
        'mpe' => 'video/mpeg',
        'mpeg' => 'video/mpeg',
        'mpg' => array(
            'audio/mpeg',
            'video/mpeg'
        ),
        'mpga' => 'audio/mpeg',
        'mpp' => 'application/vnd.ms-project',
        'mpt' => 'application/x-project',
        'mpv' => 'application/x-project',
        'mpx' => 'application/x-project',
        'mrc' => 'application/marc',
        'ms' => 'application/x-troff-ms',
        'mv' => 'video/x-sgi-movie',
        'my' => 'audio/make',
        'mzz' => 'application/x-vnd.audioexplosion.mzz',
        'nap' => 'image/naplps',
        'naplps' => 'image/naplps',
        'nc' => 'application/x-netcdf',
        'ncm' => 'application/vnd.nokia.configuration-message',
        'nif' => 'image/x-niff',
        'niff' => 'image/x-niff',
        'nix' => 'application/x-mix-transfer',
        'nsc' => 'application/x-conference',
        'nvd' => 'application/x-navidoc',
        'o' => 'application/octet-stream',
        'oda' => 'application/oda',
        'odb' => 'application/vnd.oasis.opendocument.database',
        'odc' => 'application/vnd.oasis.opendocument.chart',
        'odf' => 'application/vnd.oasis.opendocument.formula',
        'odg' => 'application/vnd.oasis.opendocument.graphics',
        'odi' => 'application/vnd.oasis.opendocument.image',
        'odm' => 'application/vnd.oasis.opendocument.text-master',
        'odp' => 'application/vnd.oasis.opendocument.presentation',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        'odt' => 'application/vnd.oasis.opendocument.text',
        'omc' => 'application/x-omc',
        'omcd' => 'application/x-omcdatamaker',
        'omcr' => 'application/x-omcregerator',
        'one' => 'application/msonenote',
        'onepkg' => 'application/msonenote',
        'onetmp' => 'application/msonenote',
        'onetoc2' => 'application/msonenote',
        'otg' => 'application/vnd.oasis.opendocument.graphics-template',
        'oth' => 'application/vnd.oasis.opendocument.text-web',
        'otp' => 'application/vnd.oasis.opendocument.presentation-template',
        'ots' => 'application/vnd.oasis.opendocument.spreadsheet-template',
        'ott' => 'application/vnd.oasis.opendocument.text-template',
        'oxt' => 'application/vnd.openofficeorg.extension',
        'p' => 'text/x-pascal',
        'p10' => array(
            'application/pkcs10',
            'application/x-pkcs10'
        ),
        'p12' => array(
            'application/pkcs-12',
            'application/x-pkcs12'
        ),
        'p7a' => 'application/x-pkcs7-signature',
        'p7c' => array(
            'application/pkcs7-mime',
            'application/x-pkcs7-mime'
        ),
        'p7m' => array(
            'application/pkcs7-mime',
            'application/x-pkcs7-mime'
        ),
        'p7r' => 'application/x-pkcs7-certreqresp',
        'p7s' => 'application/pkcs7-signature',
        'part' => 'application/pro_eng',
        'pas' => 'text/pascal',
        'pbm' => 'image/x-portable-bitmap',
        'pcl' => array(
            'application/vnd.hp-pcl',
            'application/x-pcl'
        ),
        'pct' => 'image/x-pict',
        'pcx' => 'image/x-pcx',
        'pdb' => 'chemical/x-pdb',
        'pdf' => 'application/pdf',
        'pfunk' => array(
            'audio/make',
            'audio/make.my.funk'
        ),
        'pgm' => array(
            'image/x-portable-graymap',
            'image/x-portable-greymap'
        ),
        'pic' => 'image/pict',
        'pict' => 'image/pict',
        'pkg' => 'application/x-newton-compatible-pkg',
        'pko' => 'application/vnd.ms-pki.pko',
        'pl' => array(
            'text/plain',
            'text/x-script.perl'
        ),
        'plx' => 'application/x-pixclscript',
        'pm' => array(
            'image/x-xpixmap',
            'text/x-script.perl-module'
        ),
        'pm4' => 'application/x-pagemaker',
        'pm5' => 'application/x-pagemaker',
        'png' => 'image/png',
        'pnm' => array(
            'application/x-portable-anymap',
            'image/x-portable-anymap'
        ),
        'pot' => array(
            'application/mspowerpoint',
            'application/vnd.ms-powerpoint'
        ),
        'potm' => 'application/vnd.ms-powerpoint.template.macroEnabled.12',
        'potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template',
        'pov' => 'model/x-pov',
        'ppa' => 'application/vnd.ms-powerpoint',
        'ppam' => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
        'ppm' => 'image/x-portable-pixmap',
        'pps' => array(
            'application/mspowerpoint',
            'application/vnd.ms-powerpoint'
        ),
        'ppsm' => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
        'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
        'ppt' => array(
            'application/mspowerpoint',
            'application/powerpoint',
            'application/vnd.ms-powerpoint',
            'application/x-mspowerpoint'
        ),
        'pptm' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'ppz' => 'application/mspowerpoint',
        'pre' => 'application/x-freelance',
        'prt' => 'application/pro_eng',
        'ps' => 'application/postscript',
        'psd' => 'application/octet-stream',
        'pvu' => 'paleovu/x-pv',
        'pwz' => 'application/vnd.ms-powerpoint',
        'py' => 'text/x-script.phyton',
        'pyc' => 'application/x-bytecode.python',
        'qcp' => 'audio/vnd.qcelp',
        'qd3' => 'x-world/x-3dmf',
        'qd3d' => 'x-world/x-3dmf',
        'qif' => 'image/x-quicktime',
        'qt' => 'video/quicktime',
        'qtc' => 'video/x-qtc',
        'qti' => 'image/x-quicktime',
        'qtif' => 'image/x-quicktime',
        'ra' => array(
            'audio/x-pn-realaudio',
            'audio/x-pn-realaudio-plugin',
            'audio/x-realaudio'
        ),
        'ram' => 'audio/x-pn-realaudio',
        'ras' => array(
            'application/x-cmu-raster',
            'image/cmu-raster',
            'image/x-cmu-raster'
        ),
        'rast' => 'image/cmu-raster',
        'rexx' => 'text/x-script.rexx',
        'rf' => 'image/vnd.rn-realflash',
        'rgb' => 'image/x-rgb',
        'rm' => array(
            'application/vnd.rn-realmedia',
            'audio/x-pn-realaudio'
        ),
        'rmi' => 'audio/mid',
        'rmm' => 'audio/x-pn-realaudio',
        'rmp' => array(
            'audio/x-pn-realaudio',
            'audio/x-pn-realaudio-plugin'
        ),
        'rng' => array(
            'application/ringing-tones',
            'application/vnd.nokia.ringing-tone'
        ),
        'rnx' => 'application/vnd.rn-realplayer',
        'roff' => 'application/x-troff',
        'rp' => 'image/vnd.rn-realpix',
        'rpm' => 'audio/x-pn-realaudio-plugin',
        'rt' => array(
            'text/richtext',
            'text/vnd.rn-realtext'
        ),
        'rtf' => array(
            'application/rtf',
            'application/x-rtf',
            'text/richtext'
        ),
        'rtx' => array(
            'application/rtf',
            'text/richtext'
        ),
        'rv' => 'video/vnd.rn-realvideo',
        's' => 'text/x-asm',
        's3m' => 'audio/s3m',
        'saveme' => 'application/octet-stream',
        'sbk' => 'application/x-tbook',
        'scm' => array(
            'application/x-lotusscreencam',
            'text/x-script.guile',
            'text/x-script.scheme',
            'video/x-scm'
        ),
        'sda' => array(
            'application/vnd.stardivision.draw',
            'application/x-stardraw'
        ),
        'sdc' => array(
            'application/vnd.stardivision.calc',
            'sdc application/x-starcalc'
        ),
        'sdd' => array(
            'application/vnd.stardivision.impress',
            'application/x-starimpress'
        ),
        'sdm' => 'application/vnd.stardivision.mail',
        'sdml' => 'text/plain',
        'sdp' => array(
            'application/sdp',
            'application/x-sdp',
            'application/vnd.stardivision.impress-packed'
        ),
        'sdr' => 'application/sounder',
        'sds' => array(
            'application/vnd.stardivision.chart',
            'application/x-starchart'
        ),
        'sdw' => array(
            'application/vnd.stardivision.writer',
            'application/x-starwriter'
        ),
        'sea' => array(
            'application/sea',
            'application/x-sea'
        ),
        'set' => 'application/set',
        'sgl' => 'application/vnd.stardivision.writer-global',
        'sgm' => array(
            'text/sgml',
            'text/x-sgml'
        ),
        'sgml' => array(
            'text/sgml',
            'text/x-sgml'
        ),
        'sh' => array(
            'application/x-bsh',
            'application/x-sh',
            'application/x-shar',
            'text/x-script.sh'
        ),
        'shar' => array(
            'application/x-bsh',
            'application/x-shar'
        ),
        'shtml' => array(
            'text/html',
            'text/x-server-parsed-html'
        ),
        'sid' => 'audio/x-psid',
        'sit' => array(
            'application/x-sit',
            'application/x-stuffit'
        ),
        'skd' => 'application/x-koan',
        'skm' => 'application/x-koan',
        'skp' => 'application/x-koan',
        'skt' => 'application/x-koan',
        'sl' => 'application/x-seelogo',
        'sldm' => 'application/vnd.ms-powerpoint.slide.macroEnabled.12',
        'sldx' => 'application/vnd.openxmlformats-officedocument.presentationml.slide',
        'smf' => array(
            'application/vnd.stardivision.math',
            'application/x-starmath'
        ),
        'smi' => 'application/smil',
        'smil' => 'application/smil',
        'snd' => array(
            'audio/basic',
            'audio/x-adpcm'
        ),
        'sol' => 'application/solids',
        'spc' => array(
            'application/x-pkcs7-certificates',
            'text/x-speech'
        ),
        'spl' => 'application/futuresplash',
        'spr' => 'application/x-sprite',
        'sprite' => 'application/x-sprite',
        'src' => 'application/x-wais-source',
        'ssi' => 'text/x-server-parsed-html',
        'ssm' => 'application/streamingmedia',
        'sst' => 'application/vnd.ms-pki.certstore',
        'stc' => 'application/vnd.sun.xml.calc.template',
        'std' => 'application/vnd.sun.xml.draw.template',
        'step' => 'application/step',
        'sti' => 'application/vnd.sun.xml.impress.template',
        'stl' => array(
            'application/sla',
            'application/vnd.ms-pki.stl',
            'application/x-navistyle'
        ),
        'stp' => 'application/step',
        'stw' => 'application/vnd.sun.xml.writer.template',
        'sxc' => 'application/vnd.sun.xml.calc',
        'sxd' => 'application/vnd.sun.xml.draw',
        'sxg' => 'application/vnd.sun.xml.writer.global',
        'sxi' => 'application/vnd.sun.xml.impress',
        'sxm' => 'application/vnd.sun.xml.math',
        'sxw' => 'application/vnd.sun.xml.writer',
        'sv4cpio' => 'application/x-sv4cpio',
        'sv4crc' => 'application/x-sv4crc',
        'svf' => array(
            'image/vnd.dwg',
            'image/x-dwg'
        ),
        'svr' => array(
            'application/x-world',
            'x-world/x-svr'
        ),
        'swf' => 'application/x-shockwave-flash',
        't' => 'application/x-troff',
        'talk' => 'text/x-speech',
        'tar' => 'application/x-tar',
        'tbk' => array(
            'application/toolbook',
            'application/x-tbook'
        ),
        'tcl' => array(
            'application/x-tcl',
            'text/x-script.tcl'
        ),
        'tcsh' => 'text/x-script.tcsh',
        'tex' => 'application/x-tex',
        'texi' => 'application/x-texinfo',
        'texinfo' => 'application/x-texinfo',
        'text' => array(
            'application/plain',
            'text/plain'
        ),
        'tgz' => array(
            'application/gnutar',
            'application/x-compressed'
        ),
        'thmx' => 'application/vnd.ms-officetheme',
        'tif' => array(
            'image/tiff',
            'image/x-tiff'
        ),
        'tiff' => array(
            'image/tiff',
            'image/x-tiff'
        ),
        'tr' => 'application/x-troff',
        'tsi' => 'audio/tsp-audio',
        'tsp' => array(
            'application/dsptype',
            'audio/tsplayer'
        ),
        'tsv' => 'text/tab-separated-values',
        'turbot' => 'image/florian',
        'txt' => 'text/plain',
        'uil' => 'text/x-uil',
        'uni' => 'text/uri-list',
        'unis' => 'text/uri-list',
        'unv' => 'application/i-deas',
        'uri' => 'text/uri-list',
        'uris' => 'text/uri-list',
        'ustar' => array(
            'application/x-ustar',
            'multipart/x-ustar'
        ),
        'uu' => array(
            'application/octet-stream',
            'text/x-uuencode'
        ),
        'uue' => 'text/x-uuencode',
        'vcd' => 'application/x-cdlink',
        'vcs' => 'text/x-vcalendar',
        'vda' => 'application/vda',
        'vdo' => 'video/vdo',
        'vew' => 'application/groupwise',
        'viv' => array(
            'video/vivo',
            'video/vnd.vivo'
        ),
        'vivo' => array(
            'video/vivo',
            'video/vnd.vivo'
        ),
        'vmd' => 'application/vocaltec-media-desc',
        'vmf' => 'application/vocaltec-media-file',
        'voc' => array(
            'audio/voc',
            'audio/x-voc'
        ),
        'vor' => '-',
        'vos' => 'video/vosaic',
        'vox' => 'audio/voxware',
        'vqe' => 'audio/x-twinvq-plugin',
        'vqf' => 'audio/x-twinvq',
        'vql' => 'audio/x-twinvq-plugin',
        'vrml' => array(
            'application/x-vrml',
            'model/vrml',
            'x-world/x-vrml'
        ),
        'vrt' => 'x-world/x-vrt',
        'vsd' => 'application/x-visio',
        'vst' => 'application/x-visio',
        'vsw' => 'application/x-visio',
        'w60' => 'application/wordperfect6.0',
        'w61' => 'application/wordperfect6.1',
        'w6w' => 'application/msword',
        'wav' => array(
            'audio/wav',
            'audio/x-wav'
        ),
        'wb1' => 'application/x-qpro',
        'wbmp' => 'image/vnd.wap.wbmp',
        'web' => 'application/vnd.xara',
        'wiz' => 'application/msword',
        'wk1' => 'application/x-123',
        'wmf' => 'windows/metafile',
        'wml' => 'text/vnd.wap.wml',
        'wmlc' => 'application/vnd.wap.wmlc',
        'wmls' => 'text/vnd.wap.wmlscript',
        'wmlsc' => 'application/vnd.wap.wmlscriptc',
        'word' => 'application/msword',
        'wp' => 'application/wordperfect',
        'wp5' => array(
            'application/wordperfect',
            'application/wordperfect6.0'
        ),
        'wp6' => 'application/wordperfect',
        'wpd' => 'application/wordperfect',
        'wpd' => 'application/x-wpwin',
        'wq1' => 'application/x-lotus',
        'wri' => array(
            'application/mswrite',
            'application/x-wri'
        ),
        'wrl' => array(
            'application/x-world',
            'model/vrml',
            'x-world/x-vrml'
        ),
        'wrz' => array(
            'model/vrml',
            'x-world/x-vrml'
        ),
        'wsc' => 'text/scriplet',
        'wsrc' => 'application/x-wais-source',
        'wtk' => 'application/x-wintalk',
        'xbm' => array(
            'image/x-xbitmap',
            'image/x-xbm',
            'image/xbm'
        ),
        'xdr' => 'video/x-amt-demorun',
        'xgz' => 'xgl/drawing',
        'xif' => 'image/vnd.xiff',
        'xl' => 'application/excel',
        'xla' => array(
            'application/excel',
            'application/x-excel',
            'application/x-msexcel'
        ),
        'xlam' => 'application/vnd.ms-excel.addin.macroEnabled.12',
        'xlb' => array(
            'application/excel',
            'application/vnd.ms-excel',
            'application/x-excel'
        ),
        'xlc' => array(
            'application/excel',
            'application/vnd.ms-excel',
            'application/x-excel'
        ),
        'xld' => array(
            'application/excel',
            'application/x-excel'
        ),
        'xlk' => array(
            'application/excel',
            'application/x-excel'
        ),
        'xll' => array(
            'application/excel',
            'application/vnd.ms-excel',
            'application/x-excel'
        ),
        'xlm' => array(
            'application/excel',
            'application/vnd.ms-excel',
            'application/x-excel'
        ),
        'xls' => array(
            'application/excel',
            'application/vnd.ms-excel',
            'application/x-excel',
            'application/x-msexcel'
        ),
        'xlsb' => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
        'xlsm' => 'application/vnd.ms-excel.sheet.macroEnabled.12',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xlt' => array(
            'application/excel',
            'application/x-excel'
        ),
        'xltm' => 'application/vnd.ms-excel.template.macroEnabled.12',
        'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
        'xlv' => array(
            'application/excel',
            'application/x-excel'
        ),
        'xlw' => array(
            'application/excel',
            'application/vnd.ms-excel',
            'application/x-excel',
            'application/x-msexcel'
        ),
        'xm' => 'audio/xm',
        'xml' => array(
            'application/xml',
            'text/xml'
        ),
        'xmz' => 'xgl/movie',
        'xpix' => 'application/x-vnd.ls-xpix',
        'xpm' => array(
            'image/x-xpixmap',
            'image/xpm'
        ),
        'x-png' => 'image/png',
        'xsr' => 'video/x-amt-showrun',
        'xwd' => array(
            'image/x-xwd',
            'image/x-xwindowdump'
        ),
        'xyz' => 'chemical/x-pdb',
        'z' => array(
            'application/x-compress',
            'application/x-compressed'
        ),
        'zip' => array(
            'application/x-compressed',
            'application/x-zip-compressed',
            'application/zip',
            'multipart/x-zip'
        ),
        'zoo' => 'application/octet-stream',
        'zsh' => 'text/x-script.zsh',
    );

    // Default setting values.
    private $defaults = array(
        'allowed_types' => array(),
        'filename' => null, // Tokenize filename.
        'destination_path' => null,
        'max_size' => 0, // No limit
        'fields' => array()
    );

    public function setup( Model $model, $config = array() ) {
        parent::setup( $model, $config );

        if( !empty( $this->config[ $model->alias ] ) ) {
            $this->settings[ $model->alias ] = $this->defaults;
        } else {
            $this->settings[ $model->alias ] = array_merge( $this->defaults, $config );
        }
    }

/*
    public function beforeSave( Model $model, $options = array() ) {
        parent::beforeSave( $model, $options );

        if( !$created ) {
            return;
        }


        // silent fail on no image
        if ($form_data['error'] == self::NO_FILE_UPLOAD) {
            throw new Exception ($this->errors(self::NO_FILE_UPLOAD), self::NO_FILE_UPLOAD);
        }

        $this->form_data = $form_data;

        // check we have a path - only if not returning the content
        if ($this->contentOnly === false) {
            if (empty($path) && empty($this->destinationPath)) {
                $this->form_data['error'] = self::NO_DIRECTORY_FOR_UPLOAD;
            }
        }

        // check file types
        if (!empty($this->allowedTypes)) { 
            if (!in_array($this->form_data['type'], $this->allowedTypes)) {
                $this->form_data['error'] = self::FILE_FORMAT_NOT_ALLOWED;
            }
        }

        // check max size set in code
        if ($this->maxSize > 0 && $this->form_data['size'] > $this->maxSize) {
            $this->form_data['error'] = self::FILE_SIZE_EXCEEDS_CODE_MAX;
        }

        // check error code     
        if ($this->form_data['error'] !== self::SUCCESS) {
            throw new Exception($this->errors($this->form_data['error']), $this->form_data['error']);
        }

        // if only content required read file and return
        if ($this->contentOnly) {
            return file_get_contents($this->form_data['tmp_name']);
        }

        // parse out class params to make the final destination string
        if (empty($this->filename)) {
            $destination = $this->destinationPath . $this->form_data['name'];
        } else {
            $destination = $this->destinationPath . $this->filename;
        }

        // create the destination unless otherwise set
        if ($this->createDestination) {
            $dir = dirname($destination);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
        } else {
            $dir = dirname($destination);
            if (!is_dir($dir)) {
                throw new Exception($this->errors(self::DESTINATION_NOT_AVAILABLE), self::DESTINATION_NOT_AVAILABLE);
            }
        }

        if (move_uploaded_file($this->form_data['tmp_name'], $destination)) {
            return $destination;
        } else {
            throw new Exception($this->errors(self::SERVER_WRITE_FAIL), self::SERVER_WRITE_FAIL);
        }

        // if we get here without returning something has definitely gone wrong
        throw new Exception($this->errors());
    }
*/


    public function remove( $file ) {
        $destination = $this->destinationPath . $file;
        if( is_file( $destination  ) ) {
            unlink( $destination );
        }
    }

    /**
     * parse the response type and return an error string
     * @param integer $type
     * @return string - error text
     */
    private function errors($type = null) {
        switch ($type) {
        case self::FILESIZE_EXCEED_SERVER_MAX:
            return 'File size exceeds allowed size for server';
            break;
        case self::FILESIZE_EXCEED_FORM_MAX:
            return 'File size exceeds allowed size in form';
            break;
        case self::PARTIAL_UPLOAD:
            return 'File was partially uploaded. Please check your Internet connection and try again';
            break;
        case self::NO_FILE_UPLOAD:
            return 'No file was uploaded.';
            break;
        case self::NO_DIRECTORY_FOR_UPLOAD:
            return 'No upload directory found';
            break;
        case self::SERVER_WRITE_FAIL:
            return 'Failed to write to the server';
            break;
        case self::FILE_FORMAT_NOT_ALLOWED:
            return 'File format of uploaded file is not allowed';
            break;
        case self::FILESIZE_EXCEEDS_CODE_MAX:
            return 'File size exceeds maximum allowed size';
            break;
        case self::DESTINATION_NOT_AVAILABLE:
            return 'Destination path does not exist';
            break;
        default:
            return 'There has been an unexpected error, processing upload failed';
        }
    }
}
