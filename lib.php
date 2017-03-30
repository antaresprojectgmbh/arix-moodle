<?php

require_once dirname(dirname(__FILE__)) . '/arix/arix.php';

class repository_arix extends repository
{

    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array())
    {
        global $SESSION;

        parent::__construct($repositoryid, $context, $options);
        $this->sessname = 'arix_session';
        $this->userid = optional_param('arix_username', '', PARAM_RAW);
        $this->password = optional_param('arix_password', '', PARAM_RAW);

        if (empty($SESSION->{$this->sessname}) && !empty($this->userid) && !empty($this->password)) {
            $sess = array();
            $sess['username'] = $this->userid;
            $sess['password'] = $this->password;
            $SESSION->{$this->sessname} = $sess;
        } else {
            if (!empty($SESSION->{$this->sessname})) {
                $sess = $SESSION->{$this->sessname};
                $this->userid = $sess['username'];
                $this->password = $sess['password'];
            }
        }
    }

    public function check_login()
    {
        global $SESSION;
        return !empty($SESSION->{$this->sessname});
    }

    public function print_login()
    {
        if ($this->options['ajax']) {
            $user_field = new stdClass();
            $user_field->label = get_string('username', 'repository_arix') . ': ';
            $user_field->id = 'arix_username';
            $user_field->type = 'text';
            $user_field->name = 'arix_username';

            $passwd_field = new stdClass();
            $passwd_field->label = get_string('password', 'repository_arix') . ': ';
            $passwd_field->id = 'arix_password';
            $passwd_field->type = 'password';
            $passwd_field->name = 'arix_password';

            $ret = array();
            $ret['login'] = array($user_field, $passwd_field);
            return $ret;
        } else { // Non-AJAX login form - directly output the form elements
            echo '<table>';
            echo '<tr><td><label>' . get_string('username', 'repository_arix') . '</label></td>';
            echo '<td><input type="text" name="al_username" /></td></tr>';
            echo '<tr><td><label>' . get_string('password', 'repository_arix') . '</label></td>';
            echo '<td><input type="password" name="al_password" /></td></tr>';
            echo '</table>';
            echo '<input type="submit" value="Enter" />';
        }
    }

    public static function get_instance_option_names()
    {
        return array_merge(parent::get_type_option_names(), array('arix_url', 'kontext'));
    }

    public function instance_config_form($mform)
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
        $arix_url = $this->get_option('arix_url');
        $kontext = $this->get_option('kontext');

        return new ArixClient($arix_url, $kontext, $this->userid, $this->password);
    }

    public function get_link($url)
    {
        //$arix_cli = $this->getArixCli();
        //return $arix_cli->getLink($url);
        return $url;
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
        global $SESSION;
        unset($SESSION->{$this->sessname});
        return $this->print_login();
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
