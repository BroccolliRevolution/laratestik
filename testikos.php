public static function getHtmlToDisplayPrivilegesTable($db = '*',
        $table = '*', $submit = true
    ) {
        $html_output = '';
        $sql_query = '';

        if ($db == '*') {
            $table = '*';
        }

        if (isset($GLOBALS['username'])) {
            $username = $GLOBALS['username'];
            $hostname = $GLOBALS['hostname'];
            $sql_query = self::getSqlQueryForDisplayPrivTable(
                $db, $table, $username, $hostname
            );
            $row = $GLOBALS['dbi']->fetchSingleRow($sql_query);
        }
        if (empty($row)) {
            if ($table == '*' && $GLOBALS['dbi']->isSuperuser()) {
                $row = array();
                if ($db == '*') {
                    $sql_query = 'SHOW COLUMNS FROM `mysql`.`user`;';
                } elseif ($table == '*') {
                    $sql_query = 'SHOW COLUMNS FROM `mysql`.`db`;';
                }
                $res = $GLOBALS['dbi']->query($sql_query);
                while ($row1 = $GLOBALS['dbi']->fetchRow($res)) {
                    if (mb_substr($row1[0], 0, 4) == 'max_') {
                        $row[$row1[0]] = 0;
                    } elseif (mb_substr($row1[0], 0, 5) == 'x509_'
                        || mb_substr($row1[0], 0, 4) == 'ssl_'
                    ) {
                        $row[$row1[0]] = '';
                    } else {
                        $row[$row1[0]] = 'N';
                    }
                }
                $GLOBALS['dbi']->freeResult($res);
            } elseif ($table == '*') {
                $row = array();
            } else {
                $row = array('Table_priv' => '');
            }
        }
        if (isset($row['Table_priv'])) {
            self::fillInTablePrivileges($row);

            // get columns
            $res = $GLOBALS['dbi']->tryQuery(
                'SHOW COLUMNS FROM '
                . Util::backquote(
                    Util::unescapeMysqlWildcards($db)
                )
                . '.' . Util::backquote($table) . ';'
            );
            $columns = array();
            if ($res) {
                while ($row1 = $GLOBALS['dbi']->fetchRow($res)) {
                    $columns[$row1[0]] = array(
                        'Select' => false,
                        'Insert' => false,
                        'Update' => false,
                        'References' => false
                    );
                }
                $GLOBALS['dbi']->freeResult($res);
            }
            unset($res, $row1);
        }
        // table-specific privileges
        if (! empty($columns)) {
            $html_output .= self::getHtmlForTableSpecificPrivileges(
                $username, $hostname, $db, $table, $columns, $row
            );
        } else {
            // global or db-specific
            $html_output .= self::getHtmlForGlobalOrDbSpecificPrivs($db, $table, $row);
        }
        $html_output .= '</fieldset>' . "\n";
        if ($submit) {
            $html_output .= '<fieldset id="fieldset_user_privtable_footer" '
                . 'class="tblFooters">' . "\n"
                . '<input type="hidden" name="update_privs" value="1" />' . "\n"
                . '<input type="submit" value="' . __('Go') . '" />' . "\n"
                . '</fieldset>' . "\n";
        }
        return $html_output;
    } // end of the 'PMA_displayPrivTable()' function
