<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja"><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>更新情報管理</title>
</head><body>
<h1>管理</h1>
<?php
// RSSの管理を行う画面です。
// 各種設定はphpcrssset.phpで行ってください。

include "phpcrssset.php";

$rss = new PHPCRSS($setting);
$rss->setChannel($channel);

if (getpost('pass')==$setting['pass']) {
    switch (getpost('action')) {
    case 'new':
        $rss->addData(
            mktime(
                getpost('hour'),
                getpost('minute'),
                getpost('second'),
                getpost('month'),
                getpost('day'),
                getpost('year')
            ),
            getpost('channel'),
            getpost('title'),
            getpost('url'),
            getpost('description')
        );
        break;
    case 'update': $rss->createRSS(); break;
    case 'delform': delForm(getpost('channel')); exit;
    case 'delete': $rss->deleteData(getpost('time')); delForm(getpost('channel')); exit;
    }
}

$date = localtime();
print '<form name="f" action="phpcrss.php" method="post">';
print '<table>';
print '<tr><td>タイトル</td><td><input type="text" name="title" value="" size="100"></td></tr>';
print '<tr><td>URL</td><td><input type="text" name="url" value="" size="100"></td></tr>';
print '<tr><td>説明</td><td><input type="text" name="description" value="" size="100"></td></tr>';
print '<tr><td>日時</td><td><input type="text" name="year" value="'.($date[5]+1900).'" size="4">年<input type="text" name="month" value="'.($date[4]+1).'" size="2">月<input type="text" name="day" value="'.$date[3].'" size="2">日 <input type="text" name="hour" value="'.$date[2].'" size="2">時<input type="text" name="minute" value="'.$date[1].'" size="2">分<input type="text" name="second" value="'.$date[0].'" size="2">秒</td></tr>';
print '<tr><td>パスワード</td><td><input type="password" name="pass" value="'.getpost('pass').'" size="8"> <select name="action"><option value="new">追加</option><option value="update">XMLファイル更新</option><option value="delform">削除フォームへ</option></select> <input type="submit" value="送信"></td></tr>';
print '</table>';
showRSSList($channel);
print '</form>';

function showRSSList($channel)
{
    global $rss;
    print '<ul>';
    foreach ($channel as $key => $value) {
        print '<li><label for="ch'.$key.'" onClick="document.all.f.url.value=\''.$value['link'].'\';">';
        print '<input type="radio" name="channel" value="'.$key.'" id="ch'.$key.'">';
        print $value['title'];
        print '</label>';
        if (is_array($value['channel'])) showRSSList($value['channel']);
        print '</li>';
    }
    print '</ul>';
}

function delForm($channel)
{
    global $rss;
    print '<form name="f" action="phpcrss.php" method="post">';
    print '<input type="hidden" name="channel" value="'.getpost('channel').'">';
    print 'パスワード<input type="password" name="pass" value="'.getpost('pass').'" size="16"> <select name="action"><option value="delete">削除する</option><option value="">一覧に戻る</option></select> <input type="submit" value="送信">';
    print '<dl>';
    while ($data=$rss->getData()) {
        if ($data['channel']==$channel) {
            print '<dt>'.date('y/m/d', $data['time']).' '.$data['title'].'<input type=radio name="time" value="'.$data['time'].'"></dt><dd>'.$data['description'].'</dd>';
        }
    }
    print '</dl>';
    print '</form>';
}

function getpost($name)
{
    global $_POST;
    return get_magic_quotes_gpc()?
        stripslashes($_POST[$name]):
        $_POST[$name];
}

?>
</body></html>