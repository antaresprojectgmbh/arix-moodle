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

class ArixClient
{

    public function __construct($url, $context)
    {
        if (!$url) {
            $url = "http://arix.datenbank-bildungsmedien.net/";
        }

        $this->context = $context;
        $this->password = "lxjmGktuQluiRlSlV6LFI2zyVFUuUmYk";
        $this->url = join('/', array(trim($url, '/'), trim($context, '/')));
    }

    public $searchStmt = <<<EOT
    	<search fields='titel,typ'>
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

        //$xmldata = utf8_encode($xmldata);

        return simplexml_load_string($xmldata, 'SimpleXMLElement', LIBXML_NOCDATA);
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
        $data = array('xmlstatement' => sprintf("<link id='%s'>%s</link>", $notch['id'], $phrase));
        $xml = $this->getXMLObject($data);

        return (string) $xml->a[0]->attributes()['href'] . "?play";
    }

    private function get_type($typ)
    {
        $types = array(
            19 => 'jpg',
            29 => 'mp3',
            49 => 'mpg',
            55 => 'zip',
            58 => 'html',
            69 => 'isf',
            79 => 'txt',
        );
        $typ = (int) $typ;
        if (isset($types[$typ])) {
            return $types[$typ];
        }
        return 'html'; //unknown
    }

    public function search($query)
    {
        global $CFG;

        $data = array('xmlstatement' => sprintf($this->searchStmt, $query));
        $xml = $this->getXMLObject($data);

        $result = array();
        foreach ($xml->r as $a) {
            $obj = array();
            $identifier = (string) $a->attributes()['identifier'];
            $obj['source'] = $CFG->wwwroot . '/repository/arix/redirect.php?id=' . urlencode($identifier) . '&kontext=' . urlencode($this->context);
            $obj['url'] = $CFG->wwwroot . '/repository/arix/redirect.php?id=' . urlencode($identifier) . '&kontext=' . urlencode($this->context);
            foreach ($a->f as $b) {
                switch ((string) $b->attributes()[0]) {
                    case "text":
                        $obj["text"] = (string) $b;
                        break;
                    case "titel":
                        $obj["title"] = (string) $b;
                        break;
                    case "typ":
                        $obj["typ"] = (string) $b;
                        break;
                }
            }

            $title = $obj['titel'];
            $title = substr($title, 0 - strlen($type)) . '.' . $type;
            $type = $this->get_type($obj['typ']);
            $icon = mimeinfo('icon', $title);
            $obj['thumbnail'] = $CFG->wwwroot . '/pix/f/' . $icon . '-32.png';

            array_push($result, $obj);
        }
        return $result;
    }
}
