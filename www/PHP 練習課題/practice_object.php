<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>課題</title>
  </head>
  <body>
    <?php
    // Catクラス定義
    //抽象→具体の順にコードが書かれていると思うと分かりやすい
    //classは設計図のようなもの
    class Cat {
      public $name;
      public $height;
      public $weight;
      //コンストラクタの部分は「工場」のようなもの
      //public $nameとfunction内の$nameは別物なので注意
      function __construct($name, $height, $weight) {
        $this->name = $name;
        $this->height = $height;
        $this->weight = $weight;
      }
      
      function show() {
        print "{$this->name}の身長は{$this->height}cm、体重は{$this->weight}kgです。<br>";
      }
    }
    //たまの身長は80cm、体重は30kgです。となるようにする
    // $toranekoインスタンス
    //$thisのあとに具体的なデータを記述する
    $toraneko = new Cat('たま', 80, 30);
    $toraneko->show();
    ?>
  </body>
</html>




