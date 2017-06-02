<?php
// データファイルから最新情報を表示します。
// レイアウト等はここで変更してください。
// その他の動作設定はphpcrssset.phpで行ってください。

$title = ($year = (int) $_REQUEST['year']) ? $year.'年の更新情報' : '最新情報';
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja"><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo $title; ?></title>
</head><body>
<h1><?php echo $title; ?></h1>
<p class=navi>
<?php
include 'phpcrssset.php';

$rss = new PHPCRSS($setting);

$count = 0; $out = ''; $y1 = 0;
while ($data = $rss->getData()) {
    $y2 = getdate($data['time']);
    $y2 = $y2['year'];
    if ($year == 0 ? $count < 50 : $y2 == $year) {
        ++$count;
        $out .= '<dt>'.date('y/m/d', $data['time']).' <a href="'.
            $data['link'].'">'.$data['title'].'</a></dt><dd>'.
            $data['description'].'</dd>';
    }
    if ($y1 != $y2) {
        print '<span class=nobr>[<a href="./?year='.($y1 = $y2).'">'.$y1."年</a>]</span>\n";
    }
}
?>
<span class=nobr>[<a href="rss/">RSS一覧</a>]</span>
</p>
<?php
echo '<dl>'.($count ? $out : '<dd>この年の更新情報はありませんでした。</dd>').'</dl>';
?>
</body></html>