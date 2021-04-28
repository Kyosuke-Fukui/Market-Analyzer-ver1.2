<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" type="text/css" href="https://necolas.github.io/normalize.css/8.0.1/normalize.css">
    <link rel="stylesheet" type="text/css" href="../css/style2.css?ver=??">
    <title>Market Analyzer</title>
  </head>
  <body>
    <div id=top>
      <div id="chart"></div>
      <div id="select">
        <div>
          <fieldset>
            <legend>データ選択</legend>
            <form method="post" action="../select.php">
              <label>テーブル名：
              <?php include(__DIR__ . '/../table.php'); ?>
              </label><br>
              <label>start：<input class="time1" type="datetime-local" name="start_time" value="2020-01-01T07:00:00"></label><br>
              <label>end：<input class="time2" type="datetime-local" name="end_time" value="2022-01-01T06:00:00"></label>
              <input type="hidden" name="strategy" value="oscillator">
              <input type="submit" value="グラフ表示">
            </form> 
          </fieldset>
        </div>
        <div>
          <fieldset>
          <legend>オシレーター選択</legend>
            <input type="radio" name="oscillator" value="RSI" checked></input> RSI
            <input type="radio" name="oscillator" value="RSI2" ></input> Cutler's RSI
            <input type="radio" name="oscillator" value="RCI"></input> RCI
          </fieldset>
        </div>
        <div id="btnarea">
          <button onclick="location.href='../GCDC/main.php'">GCDC戦略</button>
          <button id="calcPrice" onclick="newwindow()">価格計算</button>
          <button id="returnbtn" onclick="location.href='../index.php'">ファイルアップロード画面に戻る</button>
        </div>
      </div> 
    </div>

    <?php session_start();?>
    <script>//phpからjsへのデータ受け渡し
      let dataname = <?php echo json_encode($_SESSION['dataname']);?>;
      let data_x = <?php echo json_encode($_SESSION['time']);?>;
      let data_y = <?php echo json_encode($_SESSION['price']);?>;
    </script>
      
    <div id="bottom"> 
      <div id="backtest">
        <fieldset>
          <legend>バックテスト</legend>
          <div>
            （パラメータ設定）
            <div id="backtest-config">
              <span>RSI</span>：期間 <input id="p1" type="number" value="100" step="10" min="10"></input><br>
              買い： <input id="pLow" type="number" value="10" step="5" min="0"></input>&nbsp;
              売り： <input id="pHigh" type="number" value="90" step="5" max="100"></input><br>
              DMA： <input id="DMA" type="number" value="0" step="1" min="0"></input>
              <button id="aboutDMA">?</button><br>
              損切： <input id="risk" type="number" value="0" step="0.1" min="0"></input>%
              利確： <input id="reward" type="number" value="0" step="0.1" min="0"></input>%<br>
              トレール： <input id="TrStop" type="number" value="0" step="0.1" min="0"></input>%
              <button id="aboutTrStop">?</button>
            </div>
            <button id="backtestbtn">バックテスト実行</button>
          </div>
          <div id="pl"></div>
        </fieldset>
      </div>     
      
      <div id="optimizer">
        <fieldset> 
        <legend>パラメータ最適化
          <button id="optbtn">最適化実行</button>
        </legend>
        <div>
          <div id="opt-config">
            <span>RSI</span>: from <input id="opt-p11" type="number" value="10" step="10" min="10"></input>
            until <input id="opt-p12" type="number" value="50" step="10" min="0"></input>
            step <input id="p1-step" type="number" value="10" step="10" min="10"></input><br>
            買い: from <input id="opt-low1" type="number" value="20" step="10" min="0"></input>
            until <input id="opt-low2" type="number" value="40" step="10" min="0"></input>
            step <input id="low-step" type="number" value="10" step="10" min="10"></input><br>
            売り: from <input id="opt-high1" type="number" value="50" step="10" max="100"></input>
            until <input id="opt-high2" type="number" value="100" step="10" max="100"></input>
            step <input id="high-step" type="number" value="10" step="10" min="10"></input><br>
            DMA: from <input id="opt-DMA1" type="number" value="0" step="1" min="0"></input>
            until <input id="opt-DMA2" type="number" value="5" step="1" min="1"></input>
            step <input id="DMA-step" type="number" value="1" step="1" min="1"></input><br>
            損切: from <input id="opt-risk1" type="number" value="0.2" step="0.1" min="0"></input>%
            until <input id="opt-risk2" type="number" value="1.0" step="0.1" min="0"></input>%
            step <input id="risk-step" type="number" value="0.2" step="0.2" min="0.2"></input><br>
            利確: from <input id="opt-reward1" type="number" value="0.2" step="0.1" min="0"></input>%
            until <input id="opt-reward2" type="number" value="1.0" step="0.1" min="0"></input>%
            step <input id="reward-step" type="number" value="0.2" step="0.2" min="0.2"></input><br>
            トレール: from <input id="opt-TrStop1" type="number" value="0.0" step="0.1" min="0"></input>%
            until <input id="opt-TrStop2" type="number" value="0.0" step="0.1" min="0"></input>%
            step <input id="TrStop-step" type="number" value="0.1" step="0.1" min="0.1"></input>
          </div>
          
        </div>
        <div id="opt-result">
        </div>
        </fieldset>
      </div>
    </div>
   
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://cdn.plot.ly/plotly-latest.min.js" ></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="js/strategy.js"></script>
    <script src="js/function.js"></script>
    <script src="js/execution.js"></script>


  </body>
</html>