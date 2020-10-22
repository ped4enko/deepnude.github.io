<?php
/**
 * Archivarix Content Loader
 *
 * See README.txt for instructions with NGINX and Apache 2.x web servers
 *
 * PHP version 5.6 or newer
 * Required extensions: PDO_SQLITE
 * Recommended extensions: mbstring
 *
 * LICENSE:
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @package    Archivarix.Loader
 * @version    0.1.200406
 * @author     Archivarix Team <hello@archivarix.com>
 * @telegram   https://t.me/archivarixsupport
 * @messenger  https://m.me/ArchivarixSupport
 * @copyright  2017-2020 Archivarix LLC
 * @license    https://www.gnu.org/licenses/gpl.html GNU GPLv3
 * @link       https://archivarix.com
 */

@ini_set( 'display_errors', 0 );

/**
 * Switch mode to integrate with 3rd party CMS.
 * 0 = Default, no integration
 * 1 = Integration enabled, only missing URLs are passed to integrated CMS
 * 2 = Enabled and homepage is passed to integrated CMS
 * -1 = Return 404 error on missing urls, no integration. Disables ARCHIVARIX_FIX_*.
 */
const ARCHIVARIX_LOADER_MODE = 0;

/**
 * This parameter can have three options: http, https, any.
 * Set any for the website to work on http and https protocols
 * at the same time.
 */
const ARCHIVARIX_PROTOCOL = 'any';

/**
 * Return 1px.png if image does not exist.
 */
const ARCHIVARIX_FIX_MISSING_IMAGES = 1;

/**
 * Return empty.css if css does not exist.
 */
const ARCHIVARIX_FIX_MISSING_CSS = 1;

/**
 * Return empty.js if javascript does not exist.
 */
const ARCHIVARIX_FIX_MISSING_JS = 1;

/**
 * Return empty.ico if favicon.ico does not exist.
 */
const ARCHIVARIX_FIX_MISSING_ICO = 1;

/**
 * Redirect missing html pages.
 */
const ARCHIVARIX_REDIRECT_MISSING_HTML = '/';

/**
 * Replace a custom key-phrase with a text file or php script.
 * You can do multiple custom replaces at once by adding more
 * array element.
 * Attention! Place files into .content.xxxxxxxx/includes/ directory!
 */
const ARCHIVARIX_INCLUDE_CUSTOM = array(
  [
    'FILE'      => '', // place a file to .content.xxxxxxxx/includes/ directory and enter its filename here
    'KEYPHRASE' => '', // an entry to look for
    'LIMIT'     => 1, // how many matches to replace; -1 for unlimited
    'REGEX'     => 0, // 1 to enable perl regex (important: escape ~ symbol); 0 - disabled
    'POSITION'  => 1, // -1 to place before KEYPHRASE, 0 to replace, 1 to place after KEYPHRASE
  ],

  /**
   * Here are two most common predefined rules you may use.
   * Just fill out FILE to activate and don't forget to put
   * a file to .content.xxxxxxxx.
   */

  // before closing </head> rule, good for trackers and analytics
  [
    'FILE'      => '',
    'KEYPHRASE' => '</head>',
    'LIMIT'     => 1,
    'REGEX'     => 0,
    'POSITION'  => -1,
  ],

  // before closing </body> rule, good for counters or footer links
  [
    'FILE'      => '',
    'KEYPHRASE' => '</body>',
    'LIMIT'     => 1,
    'REGEX'     => 0,
    'POSITION'  => -1,
  ],
);

/**
 * Custom source directory name. By default this script searches
 * for .content.xxxxxxxx directory. Set the different value if you
 * renamed that directory.
 */
const ARCHIVARIX_CONTENT_PATH = '';

/**
 * Set Cache-Control header for static files.
 * By default set to 0 and Etag is used for caching.
 */
const ARCHIVARIX_CACHE_CONTROL_MAX_AGE = 2592000;

/**
 * Website can run on another domain by default.
 * Set a custom domain if it is not recognized automatically or
 * you want to run your restore on a subdomain of original domain.
 */
const ARCHIVARIX_CUSTOM_DOMAIN = '';

/**
 * XML Sitemap path. Example: /sitemap.xml
 * Do not use query in sitemap path as it will be ignored
 */
const ARCHIVARIX_SITEMAP_PATH = '';

/**
 * This feature is experimental. Catch urls with a missing content
 * and store them for later review in Archivarix CMS.
 */
const ARCHIVARIX_CATCH_MISSING = 0;

/**
 *  Do not edit under this comment
 */

const ARCHIVARIX_VERSION = '0.1.200406';

$LOADER = [
  'ARCHIVARIX_LOADER_MODE'           => ARCHIVARIX_LOADER_MODE,
  'ARCHIVARIX_PROTOCOL'              => ARCHIVARIX_PROTOCOL,
  'ARCHIVARIX_FIX_MISSING_IMAGES'    => ARCHIVARIX_FIX_MISSING_IMAGES,
  'ARCHIVARIX_FIX_MISSING_CSS'       => ARCHIVARIX_FIX_MISSING_CSS,
  'ARCHIVARIX_FIX_MISSING_JS'        => ARCHIVARIX_FIX_MISSING_JS,
  'ARCHIVARIX_FIX_MISSING_ICO'       => ARCHIVARIX_FIX_MISSING_ICO,
  'ARCHIVARIX_REDIRECT_MISSING_HTML' => ARCHIVARIX_REDIRECT_MISSING_HTML,
  'ARCHIVARIX_INCLUDE_CUSTOM'        => ARCHIVARIX_INCLUDE_CUSTOM,
  'ARCHIVARIX_CONTENT_PATH'          => ARCHIVARIX_CONTENT_PATH,
  'ARCHIVARIX_CACHE_CONTROL_MAX_AGE' => ARCHIVARIX_CACHE_CONTROL_MAX_AGE,
  'ARCHIVARIX_CUSTOM_DOMAIN'         => ARCHIVARIX_CUSTOM_DOMAIN,
  'ARCHIVARIX_SITEMAP_PATH'          => ARCHIVARIX_SITEMAP_PATH,
  'ARCHIVARIX_CATCH_MISSING'         => ARCHIVARIX_CATCH_MISSING,
];

$ARCHIVARIX_SETTINGS = array();

/**
 * @param string $sourcePath
 */
function loadLoaderSettings( $sourcePath )
{
  global $LOADER;
  $filename = $sourcePath . DIRECTORY_SEPARATOR . '.loader.settings.json';
  if ( !file_exists( $filename ) ) return;
  $data = json_decode( file_get_contents( $filename ), true );
  if ( json_last_error() !== JSON_ERROR_NONE ) return;
  if ( !is_array( $data ) ) return;
  $LOADER = array_merge( $LOADER, $data );
}

/**
 * @return string
 * @throws Exception
 */
function getSourceRoot()
{
  if ( ARCHIVARIX_CONTENT_PATH ) {
    $path = ARCHIVARIX_CONTENT_PATH;
  } else {
    $path = '';
    $list = scandir( dirname( __FILE__ ) );
    foreach ( $list as $item ) {
      if ( preg_match( '~^\.content\.[0-9a-zA-Z]+$~', $item ) && is_dir( __DIR__ . DIRECTORY_SEPARATOR . $item ) ) {
        $path = $item;
        break;
      }
    }
    if ( !$path ) {
      header( 'X-Error-Description: Directory .content.xxxxxxxx not found' );
      throw new \Exception( 'Directory .content.xxxxxxxx not found' );
    }
  }
  $absolutePath = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . $path;
  if ( !realpath( $absolutePath ) ) {
    header( 'X-Error-Description: Directory does not exist' );
    throw new \Exception( sprintf( 'Directory %s does not exist', $absolutePath ) );
  }

  return $absolutePath;
}

/**
 * @param $dsn
 * @return bool
 * @throws Exception
 */
function loadSettings( $dsn )
{
  global $ARCHIVARIX_SETTINGS;
  $pdo = new PDO( $dsn );
  $res = $pdo->query( 'SELECT param, value FROM settings' );
  if ( $res ) {
    $ARCHIVARIX_SETTINGS = $res->fetchAll( PDO::FETCH_KEY_PAIR );
  } else {
    $error = $pdo->errorInfo();
    switch ( $error[1] ) :
      case 1 :
      case 11:
        header( 'X-Error-Description: Database is corrupted or data missing.' );
        throw new \Exception( 'Database is corrupted or tables are data' );
        break;
      case 14 :
        header( 'X-Error-Description: Write permission problem.' );
        throw new \Exception( 'Write permission problem. Make sure your files are under a correct user/group and avoid using PHP in a module mode.' );
        break;
    endswitch;
  }
}

/**
 * @param string $dsn
 * @param string $url
 * @return array|false
 */
function getFileMetadata( $dsn, $url )
{
  global $LOADER;
  global $ARCHIVARIX_SETTINGS;
  if ( $LOADER['ARCHIVARIX_CUSTOM_DOMAIN'] ) {
    if ( !empty( $ARCHIVARIX_SETTINGS['www'] ) && $_SERVER['HTTP_HOST'] == $LOADER['ARCHIVARIX_CUSTOM_DOMAIN'] ) {
      $url = preg_replace( '~' . preg_quote( $LOADER['ARCHIVARIX_CUSTOM_DOMAIN'], '~' ) . '~', 'www.' . $ARCHIVARIX_SETTINGS['domain'], $url, 1 );
    } else {
      $url = preg_replace( '~' . preg_quote( $LOADER['ARCHIVARIX_CUSTOM_DOMAIN'], '~' ) . '~', $ARCHIVARIX_SETTINGS['domain'], $url, 1 );
    }
  } elseif ( !preg_match( '~^([-a-z0-9.]+\.)?' . preg_quote( $ARCHIVARIX_SETTINGS['domain'], '~' ) . '$~i', $_SERVER['HTTP_HOST'] ) ) {
    if ( !empty( $ARCHIVARIX_SETTINGS['www'] ) ) {
      $url = preg_replace( '~' . preg_quote( $_SERVER['HTTP_HOST'], '~' ) . '~', 'www.' . $ARCHIVARIX_SETTINGS['domain'], $url, 1 );
    } else {
      $url = preg_replace( '~' . preg_quote( $_SERVER['HTTP_HOST'], '~' ) . '~', $ARCHIVARIX_SETTINGS['domain'], $url, 1 );
    }
  }

  if ( preg_match( '~[?]+$~', $url ) ) {
    $urlAlt = preg_replace( '~[?]+$~', '', $url );
  } elseif ( preg_match( '~[/]+$~', $url ) ) {
    $urlAlt = preg_replace( '~[/]+$~', '', $url );
  } elseif ( !parse_url( $url, PHP_URL_QUERY ) && !parse_url( $url, PHP_URL_FRAGMENT ) ) {
    $urlAlt = $url . '/';
  } else {
    $urlAlt = $url;
  }

  define( 'ARCHIVARIX_ORIGINAL_URL', $url );

  $pdo = new PDO( $dsn );
  $sth = $pdo->prepare( 'SELECT rowid, * FROM structure WHERE (url = :url COLLATE NOCASE OR url = :urlAlt COLLATE NOCASE) AND enabled = 1 ORDER BY filetime DESC LIMIT 1' );
  $sth->execute( ['url' => $url, 'urlAlt' => $urlAlt] );
  $metadata = $sth->fetch( PDO::FETCH_ASSOC );

  return $metadata;
}

/**
 * @return string
 */
function getProtocol()
{
  if ( $_SERVER['SERVER_PORT'] == 443 || ( !empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' ) ) {
    return 'https';
  }
  return 'http';
}

/**
 * @param array $metaData
 * @param string $sourcePath
 * @param string $url
 */
function render( array $metaData, $sourcePath, $url = '' )
{
  global $LOADER;
  if ( isset( $metaData['redirect'] ) && $metaData['redirect'] ) {
    header( 'Location: ' . $metaData['redirect'] );
    http_response_code( 301 );
    exit( 0 );
  }
  $sourceFile = $sourcePath . DIRECTORY_SEPARATOR . $metaData['folder'] . DIRECTORY_SEPARATOR . $metaData['filename'];
  if ( !file_exists( $sourceFile ) ) {
    handle404( $sourcePath, $url );
    exit( 0 );
  }
  header( 'Content-Type:' . $metaData['mimetype'] );
  if ( in_array( $metaData['mimetype'], ['text/html', 'text/css', 'text/xml', 'application/javascript', 'application/x-javascript'] ) ) {
    header( 'Content-Type:' . $metaData['mimetype'] . '; charset=' . $metaData['charset'], true );
  }
  if ( in_array( $metaData['mimetype'], ['application/x-javascript', 'application/font-woff', 'application/javascript', 'image/gif', 'image/jpeg', 'image/png', 'image/svg+xml', 'image/tiff', 'image/webp', 'image/x-icon', 'image/vnd.microsoft.icon', 'image/x-ms-bmp', 'text/css', 'text/javascript'] ) ) {
    $etag = md5_file( $sourceFile );
    header( 'Etag: "' . $etag . '"' );
    if ( $LOADER['ARCHIVARIX_CACHE_CONTROL_MAX_AGE'] ) {
      header( 'Cache-Control: public, max-age=' . $LOADER['ARCHIVARIX_CACHE_CONTROL_MAX_AGE'] );
    }
    if ( isset( $_SERVER['HTTP_IF_NONE_MATCH'] ) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag ) {
      http_response_code( 304 );
      exit( 0 );
    }
  }
  if ( 0 === strpos( $metaData['mimetype'], 'text/html' ) ) {
    echo prepareContent( $sourceFile, $sourcePath );
  } else {
    $fp = fopen( $sourceFile, 'rb' );
    fpassthru( $fp );
    fclose( $fp );
  }
}

/**
 * @param $file
 * @param $sourcePath
 * @return bool|mixed|string
 */
function prepareContent( $file, $sourcePath )
{
  global $LOADER;
  $content = file_get_contents( $file );

  foreach ( $LOADER['ARCHIVARIX_INCLUDE_CUSTOM'] as $includeCustom ) {
    if ( $includeCustom['FILE'] ) {
      global $includeRule;
      $includeRule = $includeCustom;
      ob_start();
      include $sourcePath . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . $includeCustom['FILE'];
      $includedContent = preg_replace( '~\$(\d)?~', '\\\$$1', ob_get_clean() );

      if ( $includeCustom['REGEX'] ) {
        $includeCustom['KEYPHRASE'] = str_replace( '~', '\~', $includeCustom['KEYPHRASE'] );
      } else {
        $includeCustom['KEYPHRASE'] = preg_quote( $includeCustom['KEYPHRASE'], '~' );
      }

      switch ( $includeCustom['POSITION'] ) {
        case -1 :
          $includedContent = $includedContent . '${0}';
          break;
        case 1 :
          $includedContent = '${0}' . $includedContent;
          break;
      }

      $content = preg_replace( '~' . $includeCustom['KEYPHRASE'] . '~is', $includedContent, $content, $includeCustom['LIMIT'] );
    }
  }
  if ( function_exists( 'mb_strlen' ) ) {
    header( 'Content-Length: ' . mb_strlen( $content, '8bit' ), true );
  }

  return $content;
}

/**
 * @param string $sourcePath
 * @param string $url
 */
function handle404( $sourcePath, $url )
{
  global $LOADER;
  if ( $LOADER['ARCHIVARIX_CATCH_MISSING'] ) {
    global $dsn;
    $url = ARCHIVARIX_ORIGINAL_URL;

    $pdo = new PDO( $dsn );
    $pdo->exec( 'CREATE TABLE IF NOT EXISTS missing (url TEXT PRIMARY KEY, status INTEGER DEFAULT 0, ignore INTEGER DEFAULT 0)' );

    $stmt = $pdo->prepare( 'INSERT OR IGNORE INTO missing (url) VALUES(:url)' );
    $stmt->bindParam( ':url', $url, PDO::PARAM_STR );
    $stmt->execute();

    $stmt = $pdo->prepare( 'UPDATE missing SET status = status + 1 WHERE url = :url' );
    $stmt->bindParam( ':url', $url, PDO::PARAM_STR );
    $stmt->execute();
  }

  $fileType = strtolower( pathinfo( parse_url( $url, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
  switch ( true ) {
    case ( in_array( $fileType, ['jpg', 'jpeg', 'gif', 'png', 'bmp'] ) && $LOADER['ARCHIVARIX_FIX_MISSING_IMAGES'] ):
      $fileName = $sourcePath . DIRECTORY_SEPARATOR . '1px.png';
      $size     = filesize( $fileName );
      render( ['folder' => '', 'filename' => '1px.png', 'mimetype' => 'image/png', 'charset' => 'binary', 'filesize' => $size], $sourcePath );
      break;
    case ( $fileType === 'ico' && $LOADER['ARCHIVARIX_FIX_MISSING_ICO'] ):
      $fileName = $sourcePath . DIRECTORY_SEPARATOR . 'empty.ico';
      $size     = filesize( $fileName );
      render( ['folder' => '', 'filename' => 'empty.ico', 'mimetype' => 'image/x-icon', 'charset' => 'binary', 'filesize' => $size], $sourcePath );
      break;
    case( $fileType === 'css' && $LOADER['ARCHIVARIX_FIX_MISSING_CSS'] ):
      $fileName = $sourcePath . DIRECTORY_SEPARATOR . 'empty.css';
      $size     = filesize( $fileName );
      render( ['folder' => '', 'filename' => 'empty.css', 'mimetype' => 'text/css', 'charset' => 'utf-8', 'filesize' => $size], $sourcePath );
      break;
    case ( $fileType === 'js' && $LOADER['ARCHIVARIX_FIX_MISSING_JS'] ):
      $fileName = $sourcePath . DIRECTORY_SEPARATOR . 'empty.js';
      $size     = filesize( $fileName );
      render( ['folder' => '', 'filename' => 'empty.js', 'mimetype' => 'application/javascript', 'charset' => 'utf-8', 'filesize' => $size], $sourcePath );
      break;
    case ( $LOADER['ARCHIVARIX_REDIRECT_MISSING_HTML'] && $LOADER['ARCHIVARIX_REDIRECT_MISSING_HTML'] !== $_SERVER['REQUEST_URI'] ):
      header( 'Location: ' . $LOADER['ARCHIVARIX_REDIRECT_MISSING_HTML'] );
      http_response_code( 301 );
      exit( 0 );
      break;
    default:
      http_response_code( 404 );
  }
}

/**
 * @return bool
 */
function checkRedirects()
{
  global $LOADER;
  global $ARCHIVARIX_SETTINGS;
  $protocol = getProtocol();

  if ( in_array( strtolower( $LOADER['ARCHIVARIX_PROTOCOL'] ), ['http', 'https'] ) && strtolower( $LOADER['ARCHIVARIX_PROTOCOL'] ) != $protocol ) {
    $location = $LOADER['ARCHIVARIX_PROTOCOL'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header( 'Location: ' . $location );
    http_response_code( 301 );
    exit( 0 );
  }

  if ( !empty( $ARCHIVARIX_SETTINGS['non-www'] ) && 0 === strpos( $_SERVER['HTTP_HOST'], 'www.' ) ) {
    $host     = preg_replace( '~^www\.~', '', $_SERVER['HTTP_HOST'] );
    $location = $protocol . '://' . $host . $_SERVER['REQUEST_URI'];
    header( 'Location: ' . $location );
    http_response_code( 301 );
    exit( 0 );
  }

  if ( !empty( $ARCHIVARIX_SETTINGS['www'] ) && $_SERVER['HTTP_HOST'] == $ARCHIVARIX_SETTINGS['domain'] ) {
    $location = $protocol . '://www.' . $ARCHIVARIX_SETTINGS['domain'] . $_SERVER['REQUEST_URI'];
    header( 'Location: ' . $location );
    http_response_code( 301 );
    exit( 0 );
  }
}

/**
 * @param string $dsn
 */
function renderSitemapXML( $dsn )
{
  global $LOADER;
  global $ARCHIVARIX_SETTINGS;
  $pagesLimit   = 50000;
  $pageProtocol = !empty( $ARCHIVARIX_SETTINGS['https'] ) ? 'https' : getProtocol();

  if ( $LOADER['ARCHIVARIX_CUSTOM_DOMAIN'] ) {
    $domain = preg_replace( '~' . preg_quote( $LOADER['ARCHIVARIX_CUSTOM_DOMAIN'], '~' ) . '$~', '', $_SERVER['HTTP_HOST'] ) . $ARCHIVARIX_SETTINGS['domain'];
    if ( !empty( $ARCHIVARIX_SETTINGS['www'] ) && $domain == $ARCHIVARIX_SETTINGS['domain'] ) {
      $domain = 'www.' . $domain;
    }
  } elseif ( preg_match( '~^([-a-z0-9.]+\.)?' . preg_quote( $ARCHIVARIX_SETTINGS['domain'], '~' ) . '$~i', $_SERVER['HTTP_HOST'] ) ) {
    $domain = $_SERVER['HTTP_HOST'];
  } else {
    $domain = $ARCHIVARIX_SETTINGS['domain'];
    if ( !empty( $ARCHIVARIX_SETTINGS['www'] ) && $domain == $ARCHIVARIX_SETTINGS['domain'] ) {
      $domain = 'www.' . $domain;
    }
  }

  $pdo = new PDO( $dsn );
  $res = $pdo->prepare( 'SELECT count(*) FROM structure WHERE hostname = :domain AND mimetype = "text/html" AND enabled = 1 AND redirect = ""' );
  $res->execute( ['domain' => $domain] );
  $pagesCount = $res->fetchColumn();

  if ( !$pagesCount ) {
    exit( 0 );
  }

  if ( $pagesCount > $pagesLimit && empty( $_GET['id'] ) ) {
    header( 'Content-type: text/xml; charset=utf-8' );
    echo '<?xml version="1.0" encoding="UTF-8"?' . '><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    for ( $pageNum = 1; $pageNum <= ceil( $pagesCount / $pagesLimit ); $pageNum++ ) {
      echo '<sitemap><loc>' . htmlspecialchars( $pageProtocol . '://' . $_SERVER['HTTP_HOST'] . $LOADER['ARCHIVARIX_SITEMAP_PATH'] . '?id=' . $pageNum, ENT_XML1, 'UTF-8' ) . '</loc></sitemap>';
    }
    echo '</sitemapindex>';
    exit( 0 );
  }

  if ( !empty( $_GET['id'] ) && !ctype_digit( $_GET['id'] ) ) {
    http_response_code( 404 );
    exit( 0 );
  }

  if ( !empty( $_GET['id'] ) ) {
    $pageId = $_GET['id'];
    if ( $pageId < 1 || $pageId > ceil( $pagesCount / $pagesLimit ) ) {
      http_response_code( 404 );
      exit( 0 );
    }
    $pagesOffset = ( $pageId - 1 ) * $pagesLimit;
    $res         = $pdo->prepare( 'SELECT * FROM structure WHERE hostname = :domain AND mimetype = "text/html" AND enabled = 1 AND redirect = "" ORDER BY request_uri LIMIT :limit OFFSET :offset' );
    $res->execute( ['domain' => $domain, 'limit' => $pagesLimit, 'offset' => $pagesOffset] );
    $pages = $res->fetchAll( PDO::FETCH_ASSOC );
  }

  if ( empty( $_GET['id'] ) ) {
    $res = $pdo->prepare( 'SELECT * FROM structure WHERE hostname = :domain AND mimetype = "text/html" AND enabled = 1 AND redirect = "" ORDER BY request_uri' );
    $res->execute( ['domain' => $domain] );
    $pages = $res->fetchAll( PDO::FETCH_ASSOC );
  }

  header( 'Content-type: text/xml; charset=utf-8' );
  echo '<?xml version="1.0" encoding="UTF-8"?' . '><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
  foreach ( $pages as $page ) {
    echo '<url><loc>' . htmlspecialchars( $pageProtocol . '://' . $_SERVER['HTTP_HOST'] . $page['request_uri'], ENT_XML1, 'UTF-8' ) . '</loc></url>';
  }
  echo '</urlset>';
}

try {
  if ( !in_array( 'sqlite', PDO::getAvailableDrivers() ) ) {
    header( 'X-Error-Description: PDO_SQLITE driver is not enabled' );
    throw new \Exception( 'PDO_SQLITE driver is not enabled.' );
  }
  if ( 'cli' === php_sapi_name() ) {
    echo "OK" . PHP_EOL;
    exit( 0 );
  }

  $sourcePath = getSourceRoot();
  loadLoaderSettings( $sourcePath );

  if ( $LOADER['ARCHIVARIX_LOADER_MODE'] == 2 && $_SERVER['REQUEST_URI'] == '/' ) {
    include __DIR__ . DIRECTORY_SEPARATOR . 'index.php';
    exit( 0 );
  }

  $dbm = new PDO( 'sqlite::memory:' );
  if ( version_compare( $dbm->query( 'SELECT sqlite_version()' )->fetch()[0], '3.7.0' ) >= 0 ) {
    $dsn = sprintf( 'sqlite:%s%s%s', $sourcePath, DIRECTORY_SEPARATOR, 'structure.db' );
  } else {
    $dsn = sprintf( 'sqlite:%s%s%s', $sourcePath, DIRECTORY_SEPARATOR, 'structure.legacy.db' );
  }
  $dbm = null;

  loadSettings( $dsn );
  checkRedirects();

  $url = ( !empty( $ARCHIVARIX_SETTINGS['https'] ) ? 'https' : 'http' ) . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

  if ( $LOADER['ARCHIVARIX_SITEMAP_PATH'] && $LOADER['ARCHIVARIX_SITEMAP_PATH'] === parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) ) {
    renderSitemapXML( $dsn );
    exit( 0 );
  }

  $metaData = getFileMetadata( $dsn, $url );
  if ( $metaData ) {
    render( $metaData, $sourcePath, $url );
  } else {
    if ( $LOADER['ARCHIVARIX_LOADER_MODE'] == -1 ) {
      http_response_code( 404 );
      exit( 0 );
    }
    if ( $LOADER['ARCHIVARIX_LOADER_MODE'] == 0 ) {
      handle404( $sourcePath, $url );
    }
    if ( $LOADER['ARCHIVARIX_LOADER_MODE'] > 0 ) {
      include __DIR__ . DIRECTORY_SEPARATOR . 'index.php';
      exit( 0 );
    }
  }
} catch ( \Exception $e ) {
  http_response_code( 503 );
  error_log( $e );
}