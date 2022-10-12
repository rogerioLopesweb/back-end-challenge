<?php
namespace App\Servico;

class ExchangeServico
{
    #declaração de variaveis internas
    private static $simboloMoeda = "";
    private static $valorConvertido = "0";

    #recebe e trata os paramentros da url
    public static function iniciar() {
        $urlParamentros =  explode("/",$_SERVER['REQUEST_URI']);
       
        if(is_array($urlParamentros) && count($urlParamentros) > 4){
           //src/{amount}/{from}/{to}/{rate}
            $servico = trim($urlParamentros[1]);
            $amount = trim($urlParamentros[2]);
            $from = trim($urlParamentros[3]);
            $to = trim($urlParamentros[4]);
            $rate = trim($urlParamentros[5]);
            if(!empty($servico)){
                $servico = $urlParamentros[1];
                $amount = $urlParamentros[2];
                $from = $urlParamentros[3];
                $to = $urlParamentros[4];
                $rate = str_replace(",", ".", $urlParamentros[5]);
                self::converteMoeda( $from, $to, $amount, $rate );
            }
        }
        
    }

    #confome a moeda retorna o simbolo
    private static function simboloMoeda($moeda){
        switch ($moeda) {
            case "BRL":
                self::$simboloMoeda =  "R$";
                break;
            case "USD":
                self::$simboloMoeda = "$";
                break;
            case "EUR":
                self::$simboloMoeda = "€";
                break;
        }
    }
    #confome a moeda converte o valor
    #from moeda de origem
    #$to moeda a ser convertida
    #rate taxa/valor da moeda a ser convertida
    public static function converteMoeda($from, $to, $amount, $rate ){
        self::simboloMoeda($to);
        switch ($from) {
            case "BRL":
                 //http://localhost:3000/exchange/1/BRL/USD/5,2938 return 0.18890022290226
                 //http://localhost:3000/exchange/1/BRL/EUR/5,1387 return 0.19460174752369
                if($to == "USD" || $to == "EUR"){
                    self::$valorConvertido =  self::calculaConversao($amount, $rate, "*");
                    break;
                }   
            case "USD":
                //http://localhost:3000/exchange/1/USD/BRL/0,1889 return 5.2938062466914
                //http://localhost:3000/exchange/1/USD/EUR/0,9706 retunn 1.0302905419328
                if($to == "BRL" || $to == "EUR"){
                    self::$valorConvertido =  self::calculaConversao($amount, $rate, "/");
                    break;
                }   
            case "EUR":
                //http://localhost:3000/exchange/1/EUR/BRL/5,1387 return 5,1387
                //http://localhost:3000/exchange/1/EUR/USD/1,0310 return 1.031
                if($to == "BRL" || $to == "USD"){
                    self::$valorConvertido =  self::calculaConversao($amount, $rate, "*");
                    break;
                }   
        }
    }
    #calcula a conversão confome o operador 
    private static function calculaConversao($amount, $rate, $operador){
         if($operador == "*") {
            return  ($amount*$rate);
         }
         if($operador == "/") {
            return  ($amount/$rate);
         }
    }
    #imprime no formato json
    public static function printJson()
    {
        $array = array(
            "valorConvertido" => self::$valorConvertido,
            "simboloMoeda" => self::$simboloMoeda,
        );
        echo json_encode($array);
    }
}