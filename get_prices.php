<?php

    session_start();

    require_once('functions.php');

    $symbol = isset($_GET['symbol']) ? $_GET['symbol'] : '';
    $symbols;

    if (strpos($symbol, ",") !== -1) {
        $symbols = explode(",", $symbol);
        foreach ($symbols as $s) {
            print_r("<h1>".$s.get_current_price($s)."</h1>");
        }
    } else {

        //print_r(get_stock_prices($symbol, "TIME_SERIES_INTRADAY", "1min", "full", "csv"));
        print_r("<h1>".$symbol.get_current_price($symbol)."</h1>");
    }


?>