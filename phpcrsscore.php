<?php
// PHPCRSSの中枢部分。
// 基本的にこのファイルは変更しないでください。

class PHPCRSS {
    var $channel;
    // これらの設定はphpcrssset.phpで変更してください
    var $setting = array(
        'dir'=>'rss/',  // RSSを配信するディレクトリ
        'ext'=>'.xml',  // RSSの拡張子
        'data'=>'rss/phpcrss.dat',  // RSSになる前のデータファイル
        'itemmax'=>15,  // RSSに記録するアイテム数
        'echo'=>true,   // メッセージを自動的に表示するか
    );

    var $datahandle = null;

    function PHPCRSS($s)
    {
        $this->setting = array_merge($this->setting, $s);
    }
    function dispose()
    {
        if ($this->datahandle!=null) {
            fclose($this->datahandle);
            $this->datahandle = null;
        }
        if (is_array($this->channel)) {
            foreach ($this->channel as $key => $value) {
                if ($value['fp']) $this->closeRSS($key);
            }
        }
    }

    function setChannel($channel, $parent=null)
    {
        foreach ($channel as $key => $value) {
            if (isset($this->channel[$key])) die($key.'が重複しています。');
            if ($parent!=null) {
                $c = $value['channel'];
                $this->channel[$key] = array_merge($this->channel[$parent], $value);
                $this->channel[$key]['channel'] = $c;
            }else {
                $this->channel[$key] = $value;
            }
            $this->channel[$key]['parent'] = $parent;
            if (is_array($this->channel[$key]['channel'])) {
                $this->setChannel($this->channel[$key]['channel'], $key);
                unset($this->channel[$key]['channel']);
            }
        }
    }

    function open()
    {
        $this->datahandle = fopen($this->setting['data'], 'r');
    }
    function readLine()
    {
        if (!$this->datahandle) $this->open();
        return @fgets($this->datahandle);
    }
    function getData()
    {
        if (!($line=@$this->readLine())) return FALSE;
        $buf = explode("\t", $line);
        if (count($buf)<5) return FALSE;
        return array(
            'time'  =>  $buf[0],
            'channel'   =>  $buf[1],
            'title' =>  $buf[2],
            'link'  =>  $buf[3],
            'description'   =>  $buf[4],
            'line'  =>  $line,
        );
    }
    function addData($time,$channel,$title,$link,$description)
    {
        $line =
            $time."\t".
            $channel."\t".
            $title."\t".
            $link."\t".
            $description."\t\n";
        if (($tmpfile=tempnam($this->setting['dir'], '.tmp'))==false) {
            // 以前tempnamが動かなくて痛い目にあった
            $tmpfile = $this->setting['data'].'.tmp';
        }
        $tp = fopen($tmpfile, 'r+');
        while ($data=$this->getData()) {
            if ($time>$data['time']) {
                fwrite($tp, $line);
                $line=null;
            }
            fwrite($tp, $data['line']);
        }
        if ($line) fwrite($tp, $line);
        fclose($tp);
        $this->dispose();
        copy($tmpfile, $this->setting['data']);
        unlink($tmpfile);
        $this->createRSS();
    }
    function deleteData($time)
    {
        if (($tmpfile=tempnam($this->setting['dir'], '.tmp'))==false) {
            // 以前tempnamが動かなくて痛い目にあった
            $tmpfile = $this->setting['data'].'.tmp';
        }
        $tp = fopen($tmpfile, 'r+');
        while ($data=$this->getData()) {
            if ($time!=$data['time']) fwrite($tp, $data['line']);
        }
        if ($line) fwrite($tp, $line);
        fclose($tp);
        $this->dispose();
        copy($tmpfile, $this->setting['data']);
        unlink($tmpfile);
        $this->createRSS();
    }

    function createRSS()
    {
        while ($data=$this->getData()) {
            $this->writeRSSItem($data);
        }
        $this->dispose();
        if ($this->setting['echo']) echo '<p>RSSを配信しました！</p>';
    }
    function writeRSSItem($data)
    {
        $chname = $data['channel'];
        if ($this->channel[$chname]['count']<$this->setting['itemmax']) {
            if ($this->channel[$chname]['count']==0) $this->openRSS($chname, $data['time']);
            $this->channel[$chname]['count']++;
            $fp = $this->channel[$chname]['fp'];
            fwrite($fp, '<item>');
            $this->writeTag($fp, 'title', $data['title']);
            $this->writeTag($fp, 'link', $data['link']);
            $this->writeTag($fp, 'description', $data['description']);
            $this->writeTag($fp, 'pubDate', date('r', $data['time']));
            fwrite($fp, '</item>');
        }
        if ($this->channel[$chname]['parent']) {
            $data['channel'] = $this->channel[$chname]['parent'];
            $this->writeRSSItem($data);
        }
    }
    function openRSS($chname, $time)
    {
        $fp = $this->channel[$chname]['fp'] = fopen($this->setting['dir'].$chname.$this->setting['ext'], 'w');
        fwrite($fp, '<?xml version="1.0" encoding="utf-8" ?>');
        fwrite($fp, '<rss version="2.0">');
        fwrite($fp, '<channel>');
        foreach ($this->channel[$chname] as $key => $value) {
            if ($key!='channel'&&
                $key!='fp'&&
                $key!='parent') {
                $this->writeTag($fp, $key, $value);
            }
        }
        $this->writeTag($fp, 'pubDate', date('r', $time));
    }
    function closeRSS($chname)
    {
        fwrite($this->channel[$chname]['fp'], '</channel>');
        fwrite($this->channel[$chname]['fp'], '</rss>');
        fclose($this->channel[$chname]['fp']);
        $this->channel[$chname]['fp'] = null;
        $this->channel[$chname]['count'] = 0;
    }
    function writeTag($fp, $key, $value)
    { fwrite($fp, '<'.$key.'>'.htmlspecialchars($value).'</'.$key.'>'); }
}
?>