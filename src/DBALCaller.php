<?php

namespace Codeages\SCPS;

class DBALCaller
{
    public static function call($conn, $method, $args)
    {
        $statement = call_user_func_array(array($conn, $method), $args);
        $result = array();
        if ($method == 'executeUpdate') {
            $result['affectedRows'] = $statement;
            //insert 要返回lastInsertId
            if ('INSERT' == strtoupper(substr(ltrim($args[0]), 0, 6))) {
                $result['lastInsertId'] = $conn->lastInsertId();
            }
 
        } elseif ($method == 'executeQuery') {
            //executeQuery 主要是 querybuilder
            $meta = $statement->getColumnMeta(0);
            if ('COUNT' == strtoupper(substr(ltrim($meta['name']), 0, 5))) {
                $result['fetchColumn'] = $statement->fetchColumn(0);
            } else {
                $result['fetchAll'] = $statement->fetchAll();
            }
        } else {
            $result = $statement;
        }
           
        return $result;
    }
}
