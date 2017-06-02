<?php

// PHPCRSS
//
//  カテゴリーごとに階層化して表示できる
//  PHP製のRSS配信2.0プログラムです。
//
//  作者：美文
//  メール：mifumi323@tgws.fromc.jp
//  Webサイト：https://tgws.plus/
//  配布ページ：https://tgws.plus/dl/phpcrss/
//
//  更新履歴：
//   1.2 不等号などが含まれるときに正常に出力できないバグを修正
//   1.1 不要なバックスラッシュを取り除くようにした
//   1.0 公開
//
//  パーミッション：
//   *.php (604)
//   rss/  (707)

include 'phpcrsscore.php';

$setting = array(
    'pass' => 'パスワード(必ず設定してください)',
    // 他の設定項目についてはphpcrsscore.phpにある
    // 初期設定との差分だけをここに書けば反映されます。
    // 例：'itemmax'=>20,
);

// チャンネル(階層化可能)
$channel = array(
    'root' => array(
        'title' => '親要素',
        'link' => 'http://アドレス/',
        'description' => 'ここにはRSSファイルに記入される説明を書きます',
        'language' => 'ja',
        'copyright' => '著作権表記',
        'docs' => 'http://blogs.law.harvard.edu/tech/rss',
        'channel' => array(
            'child1' => array(
                'title' => '子要素１',
                'link' => 'http://アドレス/',
                'description' => '細かいジャンル分けのために子要素はあります',
            ),
            'child2' => array(
                'title' => '子要素２',
                'link' => 'http://アドレス/',
                'description' => '子要素の子要素を作ることも可能です',
                'channel' => array(
                    'child3' => array(
                        'title' => '子要素３',
                        'link' => 'http://アドレス/',
                        'description' => '子要素を更新すると親要素も更新されます',
                    ),
                ),
            ),
        ),
    ),
);
