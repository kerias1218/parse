<?php

namespace Naya;

class MySql {

    private $host;
    private $user;
    private $pass;
    private $dbname;
    private $tbl;
    private $link;

    public function __construct($host, $user, $pass, $dbname) {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->dbname = $dbname;
    }

    public function MySql() {
        $this->link = @mysqli_connect($this->host, $this->user, $this->pass);
        $this->DBError("DB CONNECT ERROR");
        mysqli_select_db($this->link, $this->dbname);
        $this->DBError("DB_SELECT_ERROR");
    }

    public function Quit() {
        mysqli_close($this->link);
    }

    public function Query( $SQL ) {
        mysqli_query($this->link, $SQL );
        $this->DBError( $SQL );

        if( preg_match("!insert into!is", $SQL) ) {
            return mysqli_insert_id($this->link);
        }
    }

    public function RowQuery( $SQL ) {
        $result = mysqli_query($this->link, $SQL );
        $this->DBError( $SQL );
        return $result;
    }

    public function OneRowQuery( $SQL ) {
        $result = mysqli_query( $this->link, $SQL );
        $this->DBError( $SQL );
        return mysqli_fetch_array( $result );
    }

    public function FieldQuery( $SQL ) {
        $result   = mysqli_query( $this->link, $SQL );
        $OneValue = mysqli_fetch_row($result);
        $this->DBError( $SQL );
        return $OneValue[0];
    }

    public function DBError( $SQL ) {
        if( $errno = mysqli_errno($this->link) ) {
           $errmsg = mysqli_error($this->link);
           echo "<script language=\"javascript\">\n";
           echo "<!--\n";
           echo "alert(\"$errno : $errmsg : $SQL \");\n";
           //echo "history.back();\n";
           echo "//-->\n";
           echo "</script>\n";
           exit;
        }
    }

    public function Back( $ErrMsg ) {
       echo "<script language=\"javascript\">\n" .
            "<!--\n" .
            "window.alert(\"$ErrMsg \");\n" .
            "history.go(-1);\n" .
            "//-->\n" .
            "</script>\n";
       exit;
    }
}
