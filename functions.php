<?php

    /*stock market; time_series functions:
        TIME_SERIES_INTRADAY
        TIME_SERIES_DAILY
        TIME_SERIES_DAILY_ADJUSTED
        TIME_SERIES_WEEKLY
        TIME_SERIES_WEEKLY_ADJUSTED
        TIME_SERIES_MONTHLY
        TIME_SERIES_MONTHLY_ADJUSTED
        BATCH_STOCK_QUOTES
    */

    /*digital_currency; time_series functions:
        DIGITAL_CURRENCY_INTRADAY
        DIGITAL_CURRENCY_DAILY
        DIGITAL_CURRENCY_WEEKLY
        DIGITAL_CURRENCY_MONTHLY
    */

    function get_stock_prices($symbol, $time_series = "TIME_SERIES_INTRADAY", $interval = "1min", $outputsize = "full", $datatype = "json") {

        return get_prices($symbol, $time_series, $interval, $outputsize, $datatype);

    }

    function get_current_price($symbol) {

        $lp = $_SESSION[$symbol]['last_price'];
        $lu = $_SESSION[$symbol]['last_updated'];

        if ($lp != null && substr($lu, strlen($lu) - 8, 8) == "15:59:00") {
            $lp = $_SESSION[$symbol]['last_price'];
            $lu = $_SESSION[$symbol]['last_updated'];
            return json_encode(array(
                'last_price' => $lp,
                'last_updated' => $lu,
                'success' => true,
                'message' => 'cached price'
            ));
        }

        $prices_json = json_decode(get_prices($symbol, 'TIME_SERIES_INTRADAY', "1min", "compact"), true);

        $prices = $prices_json['Time Series (1min)'];

        $last_price;
        $last_updated;
        $result = [];

        foreach ($prices as $key => $val) {

            $last_price = doubleval($val['4. close']);
            $last_updated = $key;

            break;

        }

        $_SESSION[$symbol]['last_price'] = $last_price;
        $_SESSION[$symbol]['last_updated'] = $last_updated;

        //str_replace required to remove commas (js parseFloat(x) doesn't like commas)
        $result['last_price'] = str_replace(',', '', $last_price);
        $result['last_updated'] = $last_updated;

        $result['success'] = (count($prices) > 0) ? true : false;
        $result['message'] = count($prices) > 0 ? "realtime price" : "no prices returned";

        return json_encode($result);

    }

    function get_prices($symbol, $time_series, $interval = "", $outputsize = "", $datatype = "json", $market = "", $from_currency = "", $to_currency = "") {

        $ch = curl_init();

        $url = "https://www.alphavantage.co/query?function=$time_series&symbol=$symbol&interval=$interval&outputsize=$outputsize&market=$market&from_currency=$from_currency&to_currency=$to_currency&apikey=YV4S1Q7722AF0LRA&datatype=$datatype";

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        error_log("curl error: ".curl_error($ch));

        curl_close($ch);

        return $result;

    }

?>