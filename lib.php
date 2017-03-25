<?php

//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors", 1);

require_once dirname(dirname(__FILE__)) . '/arix/arix.php';

/**
 * Copy remote file over HTTP one small chunk at a time.
 *
 * @param $infile The full URL to the remote file
 * @param $outfile The path where to save the file
 */
function copyfile_chunked($infile, $outfile) {
    $chunksize = 10 * (1024 * 1024); // 10 Megs

    /**
     * parse_url breaks a part a URL into it's parts, i.e. host, path,
     * query string, etc.
     */
    $parts = parse_url($infile);
    $i_handle = fsockopen($parts['host'], 80, $errstr, $errcode, 5);
    $o_handle = fopen($outfile, 'wb');

    if ($i_handle == false || $o_handle == false) {
        return false;
    }

    if (!empty($parts['query'])) {
        $parts['path'] .= '?' . $parts['query'];
    }

    /**
     * Send the request to the server for the file
     */
    $request = "GET {$parts['path']} HTTP/1.1\r\n";
    $request .= "Host: {$parts['host']}\r\n";
    $request .= "User-Agent: Mozilla/5.0\r\n";
    $request .= "Keep-Alive: 115\r\n";
    $request .= "Connection: keep-alive\r\n\r\n";
    fwrite($i_handle, $request);

    /**
     * Now read the headers from the remote server. We'll need
     * to get the content length.
     */
    $headers = array();
    while(!feof($i_handle)) {
        $line = fgets($i_handle);
        if ($line == "\r\n") break;
        $headers[] = $line;
    }

    /**
     * Look for the Content-Length header, and get the size
     * of the remote file.
     */
    $length = 0;
    foreach($headers as $header) {
        if (stripos($header, 'Content-Length:') === 0) {
            $length = (int)str_replace('Content-Length: ', '', $header);
            break;
        }
    }

    /**
     * Start reading in the remote file, and writing it to the
     * local file one chunk at a time.
     */
    $cnt = 0;
    while(!feof($i_handle)) {
        $buf = '';
        $buf = fread($i_handle, $chunksize);
        $bytes = fwrite($o_handle, $buf);
        if ($bytes == false) {
            return false;
        }
        $cnt += $bytes;

        /**
         * We're done reading when we've reached the conent length
         */
        if ($cnt >= $length) break;
    }

    fclose($i_handle);
    fclose($o_handle);
    return $cnt;
}

class repository_arix extends repository
{

    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array())
    {
        parent::__construct($repositoryid, $context, $options);
    }

    public function check_login()
    {
        return true;
    }

    public static function get_type_option_names()
    {
        return array_merge(parent::get_type_option_names(), array('arix_url', 'kontext'));
    }

    public function type_config_form($mform)
    {
        parent::type_config_form($mform);

        $arix_url = get_config('repository_arix', 'arix_url');
        $mform->addElement('text', 'arix_url', get_string('arix_url', 'repository_arix'), array('size' => '40'));
        $mform->setDefault('arix_url', $arix_url);

        $kontext = get_config('repository_arix', 'kontext');
        $mform->addElement('text', 'kontext', get_string('kontext', 'repository_arix'), array('size' => '40'));
        $mform->setDefault('kontext', $kontext);
    }

    public function get_listing($path = '', $page = '')
    {
        $list = array();
        $list['list'] = array();
        // the management interface url
        $list['manage'] = false;
        // dynamically loading
        $list['dynload'] = true;
        // set to true, the login link will be removed
        $list['nologin'] = false;
        // set to true, the search button will be removed
        $list['nosearch'] = false;
        // a file in listing
        $list['list'][] = array('title' => 'file.txt',
            'size' => '1kb',
            'date' => '2008.1.12',
            // plugin-dependent unique path to the file (id, url, path, etc.)
            'source' => '',
            // the accessible url of the file
            'url' => '',
        );

        return $list;
    }

    public function get_link($url)
    {
        return $url;
    }

    public function get_file($url, $filename = '')
    {
        $path = $this->prepare_file($filename); // Generate a unique temporary filename
        $arix_cli = new ArixClient("http://arix.datenbank-bildungsmedien.net/", "NRW");

        /*$c = new curl;
        $result = $c->download_one($arix_cli->getLink($url), null, array('filepath' => $path, 'timeout' => self::GETFILE_TIMEOUT));
        if ($result !== true) {
        throw new moodle_exception('errorwhiledownload', 'repository', '', $result);
        }*/

        copyfile_chunked($arix_cli->getLink($url), $path);
        return array('path' => $path, 'url' => $url);
    }

    public function search($text)
    {
        $search_result = array();
        $arix_cli = new ArixClient("http://arix.datenbank-bildungsmedien.net/", "NRW");
        $search_result['list'] = $arix_cli->search($text);
        $search_result['issearchresult'] = true;
        $search_result['norefresh'] = true;
        $search_result['dynload'] = true;

        return $search_result;
    }

    public function logout()
    {
        return true;
    }

    public function global_search()
    {
        return false;
    }
}
