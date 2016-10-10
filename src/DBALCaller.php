<?php

namespace Codeages\SCPS;

class DBALCaller
{
    public static function call($conn, $method, $args)
    {
        if ($method) {
            $statement = call_user_func_array(array($conn, $method), $args);

            $result = array();
            if ($method == 'executeUpdate') {
                $result = $statement;
            } else {
                $result['fetchColumn'] = $statement->fetchColumn(0);
                $result['fetchAll'] = $statement->fetchAll();
                $result['fetch'] = $statement->fetch(\PDO::FETCH_ASSOC);
            }
            
            return $result;
        }

        return "method-name:{$method}";
    }
}
