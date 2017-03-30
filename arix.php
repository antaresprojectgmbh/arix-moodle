<?php

class ArixClient
{

    public function __construct($url, $context, $userid, $password)
    {
        if (!$url) {
            $url = "http://arix.datenbank-bildungsmedien.net/";
        }

        $this->context = $context;
        $this->url = join('/', array(trim($url, '/'), trim($context, '/')));
        //$this->login($userid, $password);
    }

    public $searchStmt = <<<EOT
    	<search fields='titel'>
			<condition field='titel'>%s</condition>
		</search>
EOT;

    private function getXMLObject($data)
    {
        $options = array(
            'http' => array(
                'header' => 'Content-type: application/x-www-form-urlencoded' . "\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
            ),
        );

        $context = stream_context_create($options);
        $xmldata = file_get_contents($this->url, false, $context);

        $xmldata = utf8_encode($xmldata);

        return simplexml_load_string($xmldata, 'SimpleXMLElement', LIBXML_NOCDATA);
    }

    private function login($userid, $password)
    {
        $data = array('xmlstatement' => sprintf("<login user_id='%s' password='%s' /> ", $userid, md5($password)));
        $xml = $this->getXMLObject($data);

        $allow = $xml->attributes()['allow'];
        if ($allow == 'yes') {
            $this->userid = $xml->attributes()['tmpuser'];
            $this->password = (string) $xml;
        }
    }

    private function generatePhrase($notch)
    {
        return md5("$notch:$this->password");
    }

    public function getNotch($identifier)
    {
        $data = array('xmlstatement' => sprintf("<notch identifier='%s' />", $identifier));
        $xml = $this->getXMLObject($data);

        $result = array();
        $result['id'] = (string) $xml->attributes()[0];
        $result['notch'] = (string) $xml;

        return $result;
    }

    public function getLink($identifier)
    {
        $notch = $this->getNotch($identifier);

        $phrase = $this->generatePhrase($notch['notch']);
        $data = array('xmlstatement' => sprintf("<link id='%s' tmpuser='%s' >%s</link>", $notch['id'], $this->userid, $phrase));
        $xml = $this->getXMLObject($data);

        return (string) $xml->a[0]->attributes()['href'] . "?play";
    }

    public function search($query)
    {
        global $CFG, $USER;

        $data = array('xmlstatement' => sprintf($this->searchStmt, $query));
        $xml = $this->getXMLObject($data);

        $result = array();
        foreach ($xml->r as $a) {
            $obj = array();
            $identifier = (string) $a->attributes()['identifier'];
            $obj['source'] = $CFG->wwwroot . '/repository/arix/redirect.php?id=' . urlencode($identifier) . '&teacher=' . $USER->id . '&kontext=' . urlencode($this->context);
            $obj['url'] = $CFG->wwwroot . '/repository/arix/redirect.php?id=' . urlencode($identifier) . '&teacher=' . $USER->id . '&kontext=' . urlencode($this->context);
            $obj['thumbnail'] = 'http://localhost/playground/ic_personal_video_black_24dp_2x.png';
            foreach ($a->f as $b) {
                switch ((string) $b->attributes()[0]) {
                    case "text":
                        $obj["text"] = (string) $b;
                        break;
                    case "titel":
                        $obj["title"] = (string) $b;
                        break;
                    case "licence":
                        $obj["licence"] = (string) $b;
                        break;
                }

            }
            array_push($result, $obj);
        }
        return $result;
    }
}
