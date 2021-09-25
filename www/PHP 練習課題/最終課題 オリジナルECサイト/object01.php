<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>オブジェクト指向</title>
  </head>
  <body>
    <?php
    // Dogクラスを定義
    class Dog {
      public $name;
      public $height;
      public $weight;
      function show() {
        print "{$this->name}の身長は{$this->height}cm、体重は{$this->weight}kgです。<br>";
      }
    }
 //thisはtaroとjiroのこと
 
    // $taroインスタンス
    $taro = new Dog();
    $taro->name = '太郎'; //訳すと、taroのnameは太郎となる（左から順に）
    $taro->height = 100;
    $taro->weight = 50;
    $taro->show();
 
    // $jiroインスタンス
    $jiro = new Dog();
    $jiro->name = '次郎';
    $jiro->height = 90;
    $jiro->weight = 45;
    $jiro->show();
    ?>
  </body>
</html>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>オブジェクト指向 コンストラクタ</title>
  </head>
  <body>
    <?php
    //抽象→具体の順にコードが書かれていると思うと分かりやすい
    //classは設計図のようなもの
    class Dog {
      public $name;
      public $height;
      public $weight;
 
      // コンストラクタ
      //以下、「__construct()」として宣言したメソッドが、コンストラクタメソッドです。ここでは、3つの引数を受け取って、それぞれのプロパティの初期化を行っています。
      //コンストラクタの部分は「工場」のようなもの
      //public $nameとfunction内の$nameは別物なので注意
      function __construct($name, $height, $weight) {
        $this->name   = $name;
        $this->height = $height;
        $this->weight = $weight;
      }
      function show() {
        print "{$this->name}の身長は{$this->height}cm、体重は{$this->weight}kgです。<br>";
      }
    }

    // $taroインスタンス
    //以下、Dogクラスをインスタンス化するときに、3つのパラメータをコンストラクタメソッドに渡しています。
    $taro = new Dog('太郎', 100, 50);
    $jiro = new Dog('次郎', 110, 65);
    $taro->show();
    $jiro->show();
    ?>
  </body>
</html>


