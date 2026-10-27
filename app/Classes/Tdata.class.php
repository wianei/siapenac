<?php
/**
 * Desenvolvimento: saulo.ricardo_@hotmail.com
 */

namespace App\Classes;

class Tdata{

    /**
     *
     * Classe de manipulação de datas
     * @param Array get com os valores passados via post ou get por Global.js->showVs()
     *
     */
    function __construct() {
    }

    public static function format_time($t,$f=':') // t = seconds, f = separator 
    {
      return sprintf("%02d%s%02d%s%02d", floor($t/3600), $f, ($t/60)%60, $f, $t%60);
    }

    public static function isFimSemana($dateMysql) {
        $weekDay = date('w', strtotime($dateMysql));
        if($weekDay == 0 || $weekDay == 6)
        {
            return 1;
        }else{
            return 0;
        }
    }
    public static function TtimeStampHojeMysql()
    {
        $timesStamp = date("Y-m-d H:i:s");

        return $timesStamp;
    }

    public static function TtimeStampHojeBr()
    {
        $timesStamp = date("d/m/Y H:i:s");

        return $timesStamp;
    }

    public static function TtimeStampHojeBrTxt()
    {
        $timesStamp = date("d/m/Y H:i:s");

        $txt = substr($timesStamp, 0, 10)." às ".substr($timesStamp, 11, 8);

        return $txt;
    }
    /**
     * converte a string no formato YYYY-mm-dd hh:mm:ss para dd/mm/YYYY hh:mm:ss
     * @param $data
     * @return string
     */
    static public function TtimestampBR($data)
    {
        if(empty($data))
        {
            return false;
        }else {
            $dataMysql = substr($data, 0, 10);
            $hora = substr($data, 11, 8);
            $dataBr = self::TdataBR($dataMysql);

            return $dataBr . " " . $hora;
        }

    }
    
    /**
     * converte a string no formato dd/mm/YYYY hh:mm:ss para YYYY-mm-dd hh:mm:ss
     * @param $data
     * @return string
     */
    static public function TtimestampMysql($data)
    {
        if(empty($data))
        {
            return false;
        }else {
            $dataBr = substr($data, 0, 10);
            $hora = substr($data, 11, 8);
            $dataMysql = self::TdataMysql($dataBr);

            return $dataMysql . " " . $hora;
        }

    }
    /**
     * Converte data ano-mes-dia em dia/mes/ano (usado para MySql)
     * @param string $data
     * @return string data formatada
     */
    static public function TdataBR($data) {
        $data = implode('/', array_reverse(explode('-', $data)));
        return $data;
    }

    /**
     * Converte data dia/mes/ano em ano-mes-dia (usado para MySql)
     * @param string $data
     * @return string data formatada
     */
    static public function TdataMysql($data) {
        $data = implode('-', array_reverse(explode('/', $data)));
        return $data;
    }

    /**
     * Converte data ano-mes-dia (do banco) em inteiro anomesdia para comparação com outras datas
     */
    static public function TdataInteiro($data) {
        $data = str_replace('-', '', $data);
        return $data;
    }
    
    static public function timestampMysqlInteiro($timestamp) {
        $retirar = ["-", ":"," "];
        $data = str_replace($retirar, '', $timestamp);
        return $data;
    }

    /**
     * Retorna a data de hoje em formato MySql ano-mes-dia
     */
    static public function TdataHojeMysql() {
        return date ( "Y-m-d" );
    }

    /**
     * Retorna a data de hoje
     */
    static public function TdataHoje() {
        return date ( "d/m/Y" );
    }

    /**
     * Retorna o dia do mes atual
     * @return (int) dia
     */
    static public function dia() {
        return date ( "d" );
    }

    /**
     * Retorna o mes atual
     * @return (int) mes
     */
    static public function mes() {
        return date ( "m" );
    }

    /**
     * Retorna o ano atual
     * @return (int) ano
     */
    static public function ano() {
        return date ( "Y" );
    }
    
        /**
     * Retorna o numero do dia de hoje
     * @param (String) data no formato yyyy-mm-dd
     */
    static public function diaNumero($data = false) {
        if($data) {
            $nome = date('l', strtotime($data));
        }else{
            $nome = date('l', strtotime( self::TdataHojeMysql()));
        }

        switch ($nome) {
            case 'Sunday':
                return '0';
                break;
            case 'Monday':
                return '1';
                break;

            case 'Tuesday':
                return '2';
                break;

            case 'Wednesday':
                return '3';
                break;

            case 'Thursday':
                return '4';
                break;

            case 'Friday':
                return '5';
                break;

            case 'Saturday':
                return '6';
                break;
        }
    }


    /**
     * Retorna o nome do dia de hoje
     * @param (String) data no formato yyyy-mm-dd
     */
    static public function diaNome($data = false) {
        if($data) {
            $nome = date('l', strtotime($data));
        }else{
            $nome = date('l', strtotime( self::TdataHojeMysql()));
        }

        switch ($nome) {
            case 'Sunday':
                return 'Domingo';
                break;
            case 'Monday':
                return 'Segunda-feira';
                break;

            case 'Tuesday':
                return 'Terça-feira';
                break;

            case 'Wednesday':
                return 'Quarta-feira';
                break;

            case 'Thursday':
                return 'Quinta-feira';
                break;

            case 'Friday':
                return 'Sexta-feira';
                break;

            case 'Saturday':
                return 'Sábado';
                break;
        }
    }

    /**
     * Retorna a abreviatura do dia
     * @param (String) data no formato yyyy-mm-dd
     */
    static public function diaNomeAbreviatura($data = false) {
        if($data) {
            $nome = date('l', strtotime($data));
        }else{
            $nome = date('l', strtotime( self::TdataHojeMysql()));
        }

        switch ($nome) {
            case 'Sunday':
                return 'Dom';
                break;
            case 'Monday':
                return 'Seg';
                break;

            case 'Tuesday':
                return 'Ter';
                break;

            case 'Wednesday':
                return 'Qua';
                break;

            case 'Thursday':
                return 'Qui';
                break;

            case 'Friday':
                return 'Sex';
                break;

            case 'Saturday':
                return 'Sáb';
                break;
        }
    }

    /**
     * Retorna a hora de agora
     */
    static public function hora() {
        return  date ( "H:i:s" );
    }

    /**
     * Calcula a idade baseado na data de nascimento
     * @param $data é a data de nascimento no formato 20/09/1996
     */
    static public function getIdade($dataNascimento){

        // Separa em dia, mês e ano
        list($dia, $mes, $ano) = explode('/', $dataNascimento);

        // Descobre que dia é hoje e retorna a unix timestamp
        $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

        // Descobre a unix timestamp da data de nascimento do fulano
        $nascimento = mktime( 0, 0, 0, $mes, $dia, $ano);

        // Depois apenas fazemos o cálculo já citado :)
        $idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);

        return intval($idade);
    }


    /**
     * Retorna a data de um timestamp no formato yyyy-mm-dd
     * @param $data é o timestamp no formato yyyy-mm-dd hh:mm:ss
     */
    static public function getDataTimestamp($timestamp){
        return substr($timestamp, 0, 10);
    }

    /**
     * Retorna a data BR de um timestamp no formato dd/mm/yyyy
     * @param $data é o timestamp no formato yyyy-mm-dd hh:mm:ss
     */
    static public function getDataBrTimestamp($timestamp){
        return substr($timestamp, 8, 2)."/".substr($timestamp, 5, 2)."/".substr($timestamp, 0, 4);
    }

    /**
 * Retorna hh:mm:ss de um timestamp
 * @param $data é o timestamp no formato yyyy-mm-dd hh:mm:ss
 */
    static public function getTempoTimestamp($timestamp){
        if($timestamp == "" || is_null($timestamp) || $timestamp == null) return "";
        return substr($timestamp, -8,-3);
    }

    /**
     * verifica se é uma hora no formato hh:mm
     */
    static public function isHora($hora){
        return (strlen($hora) == 5 && $hora[2] == ':');
    }

    /**
     * Compara uma hora com a hora de agora
     * @param (String) Hora a ser comparada em formato "00::00:00"
     */
    static function compHora_agora($hora){
        $horafixa = strtotime($hora);
        $horaatual = strtotime("now");
        $rt = "igual";
        if($horaatual > $horafixa){
            return "maior";
        }

        if($horaatual < $horafixa){
            return "menor";
        }
        return $rt;
    }

    /**
     * Compara uma data com a data de hoje
     * @param (String) Data a ser comparada em formato "yyyy-mm-dd" ou dd/mm/yyyy
     */
    static function compData_hoje($data){
        $data = self::TdataMysql($data);
        $data = self::TdataInteiro($data);
        $dataHoje = date("Ymd");
        $rt = "igual";
        if($dataHoje > $data){
            $rt = "maior";
        }
        if($dataHoje < $data){
            $rt = "menor";
        }
        return $rt;
    }

    /**
     * Compara duas data1 com data2
     * @param (String) Data a ser comparada em formato "2014-02-02"
     * @param (String) Data a ser comparada em formato "2014-02-02"
     */
    static function compDatas($data1, $data2) {
        $data1 = self::TdataMysql($data1);
        $data2 = self::TdataMysql($data2);
        $data1 = self::TdataInteiro($data1);
        $data2 = self::TdataInteiro($data2);
        $rt = "igual";
        if($data1 > $data2){
            $rt = "maior";
        }
        if($data1 < $data2){
            $rt = "menor";
        }
        return $rt;
    }

    /**
     * Devolve o mês de acordo com o nome
     */
    static function getMesNum($mesExtenso) {
        $arr = array(
            'Janeiro' => '01',
            'Fevereiro' => '02',
            'Março' => '03',
            'Abril' => '04',
            'Maio' => '05',
            'Junho' => '06',
            'Julho' => '07',
            'Agosto' => '08',
            'Setembro' => '09',
            'Outubro' => '10',
            'Novembro' => '11',
            'Dezembro' => '12',
        );
        return $arr[$mesExtenso];
    }

    /**
     * Devolve o mês por extenso de acordo com o número do mês
     */
    static function getMesExt($mesnum) {
        $arr = array(
            'Janeiro' => '01',
            'Fevereiro' => '02',
            'Março' => '03',
            'Abril' => '04',
            'Maio' => '05',
            'Junho' => '06',
            'Julho' => '07',
            'Agosto' => '08',
            'Setembro' => '09',
            'Outubro' => '10',
            'Novembro' => '11',
            'Dezembro' => '12',
        );
        return array_search ($mesnum, $arr);
    }

    /**
     * Devolve o mês por extenso abreviado de acordo com o número do mês
     */
    static function getMesExtAbrev($mesnum) {
        $arr = array(
            'Jan' => '01',
            'Fev' => '02',
            'Mar' => '03',
            'Abr' => '04',
            'Mai' => '05',
            'Jun' => '06',
            'Jul' => '07',
            'Ago' => '08',
            'Set' => '09',
            'Out' => '10',
            'Nov' => '11',
            'Dez' => '12',
        );
        return array_search ($mesnum, $arr);
    }

    static function diffHour($firstTime,$lastTime,$type="")
    {
        $firstTime = strtotime($firstTime);

        $lastTime = strtotime($lastTime);

        $timeDiff = $lastTime-$firstTime;

        return intval(($timeDiff)/60);
    }
    /**
     * Subtrai duas datas em formato yyyy-mm-dd
     */
    static function diffDate($d1, $d2, $type='', $sep='-') {
        $d1 = explode($sep, $d1);
        $d2 = explode($sep, $d2);
        switch ($type){
            case 'A':
                $X = 31536000;
                break;
            case 'M':
                $X = 2592000;
                break;
            case 'D':
                $X = 86400;
                break;
            case 'H':
                $X = 3600;
                break;
            case 'MI':
                $X = 60;
                break;
            default:
                $X = 1;
        }

        $z = mktime (0, 0, 0, $d2[1], $d2[2], $d2[0]);
        $y = mktime (0, 0, 0, $d1[1], $d1[2], $d1[0]);
        return floor( ($z-$y) / $X );
    }

    public static function subDatas($dataMaior, $dataMenor)
    {
        $data1 = date_create($dataMaior);
        $data2 = date_create($dataMenor);
        $resultado = date_diff($data1, $data2);
        return date_interval_format($resultado, '%a');
    }

    /**
     * Soma dias a uma data em formato yyyy-mm-dd
     * @param unknown $data
     * @param unknown $dias
     */
    static function addDias($data, $dias) {
        return date('Y-m-d', strtotime("+$dias days",strtotime($data)));
    }

    /**
     * @param $datetime
     * @param $horas
     * @return bool|string
     */
    static function addHourToDateTime($datetime, $horas) {
        return date('Y-m-d H:i:s', strtotime($datetime.' '.$horas.' hour'));
    }

    /**
     * Subtrai dias de uma data em formato yyyy-mm-dd
     * @param unknown $data
     * @param unknown $dias
     */
    static function subDias($data, $dias) {
        return date('Y-m-d', strtotime("-$dias days",strtotime($data)));
    }

    /**
     * Retorna o dia de uma data em formato yyyy-mm-dd
     * @param Data em formato yyyy-mm-dd
     */
    static function getDiaData($data) {
        if($data == "" || is_null($data) || $data == null) return "";
        $data = explode('-', $data);
        return $data[2];
    }

    /**
     * Retorna o mes de uma data em formato yyyy-mm-dd
     * @param Data em formato yyyy-mm-dd
     */
    static function getMesData($data) {
        if (\DateTime::createFromFormat('Y-m-d', $data) !== false) {
            if($data == "" || is_null($data) || $data == null) return "";
            $data = explode('-', $data);
            return $data[1];
        }
            
        return null;
    }

    /**
     * Retorna o ano de uma data em formato yyyy-mm-dd
     * @param Data em formato yyyy-mm-dd
     */
    static function getAnoData($data) {
        if($data == "" || is_null($data) || $data == null) return "";
        $data = explode('-', $data);
        return $data[0];
    }

    /**
     * Obtem as datas entre dois intervalos de datas
     * @param (String) $dtin em formato yyyy-mm-dd
     * @param (String) $dtout em formato yyyy-mm-dd
     * @return Array de String
     */
    static function getIntervaloDatas ($dtin, $dtout) {
        if($dtin == $dtout) {
            return array($dtin, $dtout);
        }
        $dias = false;
        // Data de entrada
        $dtin = gmdate("Y-m-d", strtotime($dtin));
        // data de saída
        $dtout = gmdate("Y-m-d", strtotime($dtout));
        // Insere a data de entrada para retorno
        $dias[] = $dtin;
        // Data auxiliar
        $dataAux = $dtin;
        while($dataAux < $dtout) {
            $dataAux = gmdate("Y-m-d", strtotime("+1 day", strtotime($dataAux)));
            $dias[] = $dataAux;
        }
        return $dias;
    }

}

?>