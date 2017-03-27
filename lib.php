<?php

//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors", 1);

require_once dirname(dirname(__FILE__)) . '/arix/arix.php';

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

    private function getArixCli()
    {
        $arix_url = 'http://arix.datenbank-bildungsmedien.net/';
        $kontext = 'NRW';
        //$arix_url = get_config('repository_arix', 'arix_url');
        //$kontext = get_config('repository_arix', 'kontext');
        return new ArixClient($arix_url, $kontext);
    }

    public function get_link($url)
    {
        $arix_cli = $this->getArixCli();
        return $arix_cli->getLink($url);
    }

    public function search($text)
    {
        $search_result = array();
        $arix_cli = $this->getArixCli();
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

    public function supported_returntypes()
    {
        return FILE_EXTERNAL;
    }
}
