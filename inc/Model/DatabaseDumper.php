<?php

// 直接アクセスの防止。
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WordPress データベースのダンプ/リストアを行うモデル。
 */
class BF_SB_Model_DatabaseDumper {

    /**
     * 直近のエラーメッセージ。
     *
     * @var string
     */
    protected $last_error = '';

    /**
     * 直近のエラーメッセージを返します。
     *
     * @return string
     */
    public function get_last_error() {
        return $this->last_error;
    }

    /**
     * プレフィックスに一致する WordPress のテーブル一覧を取得します。
     *
     * @return string[] テーブル名の配列。
     */
    public function get_wp_tables() {
        global $wpdb;

        if ( ! is_object( $wpdb ) || ! method_exists( $wpdb, 'get_col' ) ) {
            $this->last_error = 'wpdb is not available.';
            return array();
        }

        $prefix = isset( $wpdb->base_prefix ) ? $wpdb->base_prefix : $wpdb->prefix;
        $tables = (array) $wpdb->get_col( 'SHOW TABLES' );

        $filtered = array();
        foreach ( $tables as $t ) {
            if ( 0 === strpos( $t, $prefix ) ) {
                $filtered[] = $t;
            }
        }

        return $filtered;
    }

    /**
     * データベースを SQL としてダンプします。
     *
     * @param string $output_file 保存先ファイルパス。
     * @return bool 成功時 true。
     */
    public function dump( $output_file ) {
        global $wpdb;
        $this->last_error = '';

        $tables = $this->get_wp_tables();
        if ( empty( $tables ) ) {
            if ( '' === $this->last_error ) {
                $this->last_error = 'No WordPress tables found.';
            }
            return false;
        }

        $dir = dirname( $output_file );
        if ( ! is_dir( $dir ) && ! wp_mkdir_p( $dir ) ) {
            $this->last_error = 'Failed to create directory: ' . $dir;
            return false;
        }

        $fh = @fopen( $output_file, 'wb' );
        if ( ! $fh ) {
            $this->last_error = 'Failed to open file for writing: ' . $output_file;
            return false;
        }

        $header = "-- BF Simple Backup SQL Dump\n";
        $header .= '-- Generation Time: ' . gmdate( 'Y-m-d H:i:s' ) . " UTC\n\n";
        $header .= "SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';\n";
        $header .= "SET time_zone = '+00:00';\n";
        $header .= "SET foreign_key_checks = 0;\n\n";
        fwrite( $fh, $header );

        foreach ( $tables as $table ) {
            // CREATE 文の出力。
            $row = $wpdb->get_row( 'SHOW CREATE TABLE `' . $table . '`', ARRAY_N );
            if ( ! $row || ! isset( $row[1] ) ) {
                $this->last_error = 'SHOW CREATE TABLE failed for ' . $table;
                fclose( $fh );
                return false;
            }

            fwrite( $fh, '--\n-- Table structure for table `' . $table . "`\n--\n\n" );
            fwrite( $fh, 'DROP TABLE IF EXISTS `' . $table . "`;\n" );
            fwrite( $fh, $row[1] . ";\n\n" );

            // データの出力。
            fwrite( $fh, '--\n-- Dumping data for table `' . $table . "`\n--\n" );
            $rows = $wpdb->get_results( 'SELECT * FROM `' . $table . '`', ARRAY_A );
            if ( empty( $rows ) ) {
                fwrite( $fh, "\n" );
                continue;
            }

            $columns = array_keys( $rows[0] );
            $col_list = '`' . implode( '`, `', $columns ) . '`';

            $batch = array();
            $batch_size = 100;
            foreach ( $rows as $r ) {
                $batch[] = '(' . $this->implode_row_values( $r ) . ')';
                if ( count( $batch ) >= $batch_size ) {
                    fwrite( $fh, 'INSERT INTO `' . $table . '` (' . $col_list . ') VALUES ' . implode( ',', $batch ) . ";\n" );
                    $batch = array();
                }
            }
            if ( ! empty( $batch ) ) {
                fwrite( $fh, 'INSERT INTO `' . $table . '` (' . $col_list . ') VALUES ' . implode( ',', $batch ) . ";\n" );
            }
            fwrite( $fh, "\n" );
        }

        fwrite( $fh, "SET foreign_key_checks = 1;\n" );
        fclose( $fh );
        return true;
    }

    /**
     * ダンプからデータベースをリストアします。
     *
     * @param string $input_file 読み込むSQLファイル。
     * @return bool 成功時 true。
     */
    public function restore( $input_file ) {
        global $wpdb;
        $this->last_error = '';

        if ( ! is_readable( $input_file ) ) {
            $this->last_error = 'Input file is not readable: ' . $input_file;
            return false;
        }

        $sql = file_get_contents( $input_file );
        if ( false === $sql ) {
            $this->last_error = 'Failed to read input file: ' . $input_file;
            return false;
        }

        $wpdb->query( 'SET foreign_key_checks = 0' );

        $buffer = '';
        $lines  = preg_split( "/\r?\n/", $sql );
        foreach ( $lines as $line ) {
            if ( '' === $line || 0 === strpos( ltrim( $line ), '--' ) ) {
                continue; // コメントや空行はスキップ。
            }
            $buffer .= $line . "\n";
            if ( preg_match( '/;\s*$/', $line ) ) {
                $result = $wpdb->query( $buffer );
                if ( false === $result ) {
                    $this->last_error = 'Query failed: ' . $buffer;
                    $wpdb->query( 'SET foreign_key_checks = 1' );
                    return false;
                }
                $buffer = '';
            }
        }

        $wpdb->query( 'SET foreign_key_checks = 1' );
        return true;
    }

    /**
     * 行データをSQL値リストに整形します。
     *
     * @param array $row 連想配列の1行。
     * @return string カンマ区切りの値リスト。
     */
    protected function implode_row_values( $row ) {
        global $wpdb;
        $values = array();
        foreach ( $row as $v ) {
            if ( is_null( $v ) ) {
                $values[] = 'NULL';
                continue;
            }
            if ( is_int( $v ) || is_float( $v ) ) {
                $values[] = (string) $v;
                continue;
            }
            $escaped = $this->escape_string( (string) $v );
            $values[] = "'{$escaped}'";
        }
        return implode( ',', $values );
    }

    /**
     * 文字列をSQL用にエスケープします。
     *
     * @param string $value 値。
     * @return string エスケープ済み。
     */
    protected function escape_string( $value ) {
        global $wpdb;
        if ( isset( $wpdb ) && isset( $wpdb->dbh ) && function_exists( 'mysqli_real_escape_string' ) ) {
            return mysqli_real_escape_string( $wpdb->dbh, $value );
        }
        return addslashes( $value );
    }
}

