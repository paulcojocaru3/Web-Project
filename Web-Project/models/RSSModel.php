<?php
class RSSModel {
    private $dom;
    private $xpath;
    
    public function __construct() {
        $this->dom = new DOMDocument();
        libxml_use_internal_errors(true);
    }
    
    public function processEventsToRSS($events) {
        $this->dom->formatOutput = true;
        
        $rss = $this->dom->createElement('rss');
        $rss->setAttribute('version', '2.0');
        $rss->setAttribute('xmlns:atom', 'http://www.w3.org/2005/Atom');
        $this->dom->appendChild($rss);
        
        $channel = $this->dom->createElement('channel');
        $rss->appendChild($channel);
        
        $channel->appendChild($this->dom->createElement('title', 'Iasi Joaca - Evenimente Sportive'));
        $channel->appendChild($this->dom->createElement('link', 'http://localhost/Web-Project/'));
        $channel->appendChild($this->dom->createElement('description', 'Evenimente sportive viitoare'));
        $channel->appendChild($this->dom->createElement('language', 'ro-ro'));
        $channel->appendChild($this->dom->createElement('lastBuildDate', date(DATE_RSS)));
        
        $atomLink = $this->dom->createElement('atom:link');
        $atomLink->setAttribute('href', 'http://localhost/Web-Project/views/rss.php');
        $atomLink->setAttribute('rel', 'self');
        $atomLink->setAttribute('type', 'application/rss+xml');
        $channel->appendChild($atomLink);
        
        foreach ($events as $event) {
            $item = $this->dom->createElement('item');
            
            $title = $this->dom->createElement('title', htmlspecialchars($event['event_name']));
            $item->appendChild($title);
            
            $link = $this->dom->createElement('link', 
                'http://localhost/Web-Project/views/view_event.php?id=' . $event['event_id']);
            $item->appendChild($link);
            
            $description = $this->dom->createElement('description');
            $cdata = $this->dom->createCDATASection(
                '<p><strong>Data:</strong> ' . date('d.m.Y H:i', strtotime($event['event_date'])) . '</p>' .
                '<p><strong>Locație:</strong> ' . htmlspecialchars($event['location']) . '</p>' .
                '<p><strong>Organizator:</strong> ' . htmlspecialchars($event['organizer_name']) . '</p>' .
                '<p><strong>Participanți:</strong> ' . $event['current_participants'] . '</p>'
            );
            $description->appendChild($cdata);
            $item->appendChild($description);
            
            $pubDate = $this->dom->createElement('pubDate', date(DATE_RSS, strtotime($event['created_at'])));
            $item->appendChild($pubDate);
            
            $guid = $this->dom->createElement('guid', 
                'http://localhost/Web-Project/views/view_event.php?id=' . $event['event_id']);
            $item->appendChild($guid);
            
            $channel->appendChild($item);
        }
        
        return $this->dom->saveXML();
    }
    
    public function parseRSSFeed($xml) {
        $this->dom->loadXML($xml);
        $this->xpath = new DOMXPath($this->dom);
        $this->xpath->registerNamespace('atom', 'http://www.w3.org/2005/Atom');
        
        $events = [];
        
        $items = $this->xpath->query('//item');
        
        foreach ($items as $item) {
            $event = [];
            
            $event['title'] = $this->getNodeValue($item, 'title');
            $event['link'] = $this->getNodeValue($item, 'link');
            $event['pubDate'] = $this->getNodeValue($item, 'pubDate');
            
            $description = $this->xpath->query('description', $item)->item(0);
            if ($description) {
                $event['description'] = $description->nodeValue;
            }
            
            $events[] = $event;
        }
        
        return $events;
    }
    
    private function getNodeValue($item, $nodeName) {
        $node = $this->xpath->query($nodeName, $item)->item(0);
        return $node ? $node->nodeValue : '';
    }
}