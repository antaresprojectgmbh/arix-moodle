<?php

class ArixClient
{

    public function __construct($url, $context)
    {
        $this->url = join('/', array(trim($url, '/'), trim($context, '/')));
    }

    public $searchStmt = <<<EOT
    	<search fields='titel'>
			<condition field='text_fields'>%s</condition>
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

        //TODO - woher soll das Password kommen ?
        $phrase = $this->generatePhrase($notch['notch']);
        $data = array('xmlstatement' => sprintf("<link id='%s'>%s</link>", $notch['id'], $phrase));
        $xml = $this->getXMLObject($data);

        return (string) $xml->a[0]->attributes()['href'] . "?play";
    }

    public function search($query)
    {
        $data = array('xmlstatement' => sprintf($this->searchStmt, $query));
        $xml = $this->getXMLObject($data);

        $result = array();
        foreach ($xml->r as $a) {
            $obj = array();
            $obj['source'] = (string) $a->attributes()[0];
            $obj['url'] = (string) $a->attributes()[0];
            //$obj['url'] = $this->getLink($obj['source']);
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
