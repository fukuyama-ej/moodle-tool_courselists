courselists
===========

moodle内のコース一覧を表示します。

**(ただし、ページング等を一切せずに出力するので、コース登録が多い場合は
かなりブラウザ側の負担になる可能性があります)**

出力される項目は以下の通りです。

* ID
* カテゴリ(親カテゴリを追っかけます)
* 名称(fullname)
* 省略名(shortname)
* IDナンバー
* 開講日
* 最大アップロードサイズ
* 作成日
* 更新日


Install
-------
{$moodlewww}/admin/tool/ 以下にmasterを展開します。
```
{$moodlewww}/admin/tool/courselists/index.php ...
```

もしくは、gitコマンドでcloneします。
```
$ cd {$moodlewww}/admin/tool
$ git clone https://github.com/fukuyama-ej/moodle-tool_courselists.git courselists
```

ログインし直すか、サイト管理→通知 にてアドオンを登録します。


Usage
-------

```
サイト管理 → コース → コースリストの表示
```

というメニューが出来ていると思いますので、それをクリックします。


Extra Function
---------------

コース一覧をCSVで出力できます。また、カテゴリ一覧もCSVで出力できるようにしてあります。
このCSV出力の際、「Upload courses / Upload categories互換出力」のチェックを入れると**コース/カテゴリの一括アップロード**に対応した出力にしている、つもりです。

しかし、いろいろと制約があり(例えば2.5まで版のコースの一括アップロードではsummaryが空だと入らない。2.6は未確認)、またコースカテゴリの扱いもmoodle2.5までと、2.6からの仕様が異なっていたりする為、試行錯誤しており、テストも不十分です。

従って、基本的には出力されたCSVを見る程度に留めておく方がよいかと思います。


免責事項
--------
MITライセンスに準拠いたします。
このアドオンを入れてコース一覧を見るだけであればそうおかしな事にはならないと思います。
しかし、CSV出力を一括アップロードする場合（特にカテゴリではなくコース)は極めてexperimentalな動きをすると思います。


ライセンス
----------
This software is released under the MIT License, see LICENSE.txt.
