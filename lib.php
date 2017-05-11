<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
 
/**
 * @package   repository_arix
 * @copyright 2017, ANTARES PROJECT GmbH
 * @author    Rene Kaufmann <kaufmann.r@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once dirname(dirname(__FILE__)) . '/arix/arix.php';

class repository_arix extends repository
{

    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array())
    {
        parent::__construct($repositoryid, $context, $options);
    }

    public static function get_instance_option_names()
    {
        return array_merge(parent::get_type_option_names(), array('arix_url', 'kontext'));
    }

    static function instance_config_form($mform)
    {
        parent::type_config_form($mform);

        $arix_url = get_config('repository_arix', 'arix_url');
        $mform->addElement('text', 'arix_url', get_string('arix_url', 'repository_arix'), array('size' => '40'));
        $mform->setType('arix_url', PARAM_NOTAGS);
        $mform->setDefault('arix_url', $arix_url);

        $kontext = get_config('repository_arix', 'kontext');
        $mform->addElement('text', 'kontext', get_string('kontext', 'repository_arix'), array('size' => '40'));
        $mform->setType('kontext', PARAM_NOTAGS);
        $mform->setDefault('kontext', $kontext);
    }

    public function print_login() {
        $keyword = new stdClass();
        $keyword->label = get_string('keyword', 'repository_wikimedia').': ';
        $keyword->id    = 'input_text_keyword';
        $keyword->type  = 'text';
        $keyword->name  = 'wikimedia_keyword';
        $keyword->value = '';
        if ($this->options['ajax']) {
            $form = array();
            $form['login'] = array($keyword);
            $form['nologin'] = true;
            $form['norefresh'] = true;
            $form['nosearch'] = true;
			return $form;
		}
	}

    public function check_login() {
        $this->keyword = optional_param('wikimedia_keyword', '', PARAM_RAW);
        if (empty($this->keyword)) {
            $this->keyword = optional_param('s', '', PARAM_RAW);
        }
        return !empty($this->keyword);
    }

    public function get_listing($path = '', $page = '')
    {
        $search_result = array();
        $arix_cli = $this->getArixCli();
	$search_result['list'] = $arix_cli->search($this->keyword, $this->id);
        $search_result['issearchresult'] = true;
        $search_result['norefresh'] = true;
        $search_result['dynload'] = true;

        return $search_result;
    }

    private function getArixCli()
    {
        $arix_url = $this->get_option('arix_url');
        $kontext = $this->get_option('kontext');

        return new ArixClient($arix_url, $kontext);
    }

    public function get_link($url)
    {
        return $url;
    }

    public function search($text, $page = 0)
    {
        $search_result = array();
        $arix_cli = $this->getArixCli();
        $search_result['list'] = $arix_cli->search($text, $this->id);
        $search_result['issearchresult'] = true;
        $search_result['norefresh'] = true;
        $search_result['dynload'] = true;

        return $search_result;
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
