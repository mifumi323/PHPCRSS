<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja"><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>RSS一覧</title>
</head><body>
<h1>RSS一覧</h1>
<p class=info>RSSリーダーを使っている場合、RSSを取得することで簡単に更新情報を得ることができます。</p>
<?php
include "../phpcrssset.php";

$rss = new PHPCRSS($setting);
showRSSList($channel);

function showRSSList($channel)
{
    global $rss;
    print '<ul>';
    foreach ($channel as $key => $value) {
        $xml = $key.$rss->setting['ext'];
        print '<li>';
        print file_exists($xml)?
            '<a href="'.$xml.'" title="'.$value['description'].'">'.$value['title'].'</a>':
            $value['title'].'(Coming Soon!)';
        if (is_array($value['channel'])) showRSSList($value['channel']);
        print '</li>';
    }
    print '</ul>';
}

?>
</body></html>