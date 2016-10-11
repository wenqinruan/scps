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

            if ('INSERT' == strtoupper(substr(ltrim($args[0]), 0, 6))) {
                $result['lastInsertId'] = $conn->lastInsertId();
            }
 
        } elseif ($method == 'executeQuery') {
            if ($statement->columnCount() == 1) {
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
