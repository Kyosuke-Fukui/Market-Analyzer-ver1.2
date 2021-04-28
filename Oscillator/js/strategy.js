let RCI = (values, period) => {
  let result = [];
  for (let i = 0; i < period - 1; i++) {
    result.push(NaN);
  }
  for (let end = period - 1; end < values.length; end++) {
    let start = end - period + 1;
    let target = values.slice(start, end + 1);
    let target_sorted = values.slice(start, end + 1).sort((a, b) => {
      return b - a;
    });
    let j = 0;
    let d = 0;
    while (j < period) {
      let time_rank = period - j;
      let price_rank = target_sorted.indexOf(target[j]) + 1;
      d += (time_rank - price_rank) * (time_rank - price_rank);
      j += 1;
    }
    let rci = (6 * d) / (period * (period * period - 1));
    rci = (1 - rci) * 100;
    result.push(rci);
  }
  return result;
};

//ワイルダー式RSI(MT4の標準RSI)
let RSI = (values, period) => {
  let result = [];
  for (let i = 0; i < period - 1; i++) result.push(NaN);

  let UpBuffer = [];
  let DnBuffer = [];

  for (let j = period - 1; j < values.length; j++) {
    let rsi;
    if (j == period - 1) {
      let sumGain = 0;
      let sumLoss = 0;
      let k = j - period + 1;

      while (k < j) {
        let difference = values[k + 1] - values[k];
        if (difference >= 0) {
          sumGain += difference;
        } else {
          sumLoss -= difference;
        }
        k += 1;
      }
      UpBuffer.push(sumGain / period);
      DnBuffer.push(sumLoss / period);
      rsi = (sumGain / (sumGain + sumLoss)) * 100;
    } else {
      let difference = values[j] - values[j - 1];
      if (difference >= 0) {
        UpBuffer.push(
          (UpBuffer[j - period] * (period - 1) + 2 * difference) / (period + 1)
        );
        DnBuffer.push((DnBuffer[j - period] * (period - 1)) / (period + 1));
      } else {
        UpBuffer.push((UpBuffer[j - period] * (period - 1)) / (period + 1));
        DnBuffer.push(
          (DnBuffer[j - period] * (period - 1) - 2 * difference) / (period + 1)
        );
      }
      rsi =
        (UpBuffer[j - period + 1] /
          (UpBuffer[j - period + 1] + DnBuffer[j - period + 1])) *
        100;
    }
    result.push(rsi);
  }

  return result;
};

//カトラー式RSI
let RSI2 = (values, period) => {
  let result = [];
  for (let i = 0; i < period - 1; i++) result.push(NaN);

  for (let j = period - 1; j < values.length; j++) {
    let sumGain = 0;
    let sumLoss = 0;
    let k = j - period + 1;
    while (k < j) {
      let difference = values[k + 1] - values[k];
      if (difference >= 0) {
        sumGain += difference;
      } else {
        sumLoss -= difference;
      }
      k += 1;
    }

    let rsi = (sumGain / (sumGain + sumLoss)) * 100;
    result.push(rsi);
  }

  return result;
};

//シグナル配列を返す関数
var Signal = function (mArray, pLow, pHigh, dma) {
  var result = [0];
  for (let i = 1; i < mArray.length; i++) {
    //インジケータがpLow以下になれば買い
    if (mArray[i - 1] > pLow && mArray[i] <= pLow) {
      result.push(1);
      //インジケータがpHigh以上になれば売り
    } else if (mArray[i - 1] < pHigh && mArray[i] >= pHigh) {
      result.push(-1);
    } else {
      result.push(0);
    }
  }

  //シグナル発生をDMAパラメータ分だけ遅らせる
  if (dma > 0) {
    for (let j = 1; j < dma + 1; j++) {
      result.unshift(0);
      result.pop();
    }
  }
  return result;
};

//分析対象のデータ群を設定する関数
var getDataSet = function (mArray, p1, pLow, pHigh, dma) {
  var rawdata = mArray;
  var oscillator = $("input[name=oscillator]:checked").val();
  var ind;
  if (oscillator == "RSI") {
    ind = RSI(rawdata, p1);
  } else if (oscillator == "RSI2") {
    ind = RSI2(rawdata, p1);
  } else {
    ind = RCI(rawdata, p1);
  }
  var sigarr = Signal(ind, pLow, pHigh, dma);
  return [rawdata, ind, sigarr];
};
