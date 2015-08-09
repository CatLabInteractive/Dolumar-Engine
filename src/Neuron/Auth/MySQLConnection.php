<?php
/**
 *  Dolumar engine, php/html MMORTS engine
 *  Copyright (C) 2009 Thijs Van der Schaeghe
 *  CatLab Interactive bvba, Gent, Belgium
 *  http://www.catlab.eu/
 *  http://www.dolumar.com/
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License along
 *  with this program; if not, write to the Free Software Foundation, Inc.,
 *  51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

/**
 * The Auth_OpenID_DatabaseConnection class, which is used to emulate
 * a PEAR database connection.
 *
 * @package OpenID
 * @author JanRain, Inc. <openid@janrain.com>
 * @copyright 2005-2008 Janrain, Inc.
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache
 */

/**
 * An empty base class intended to emulate PEAR connection
 * functionality in applications that supply their own database
 * abstraction mechanisms.  See {@link Auth_OpenID_SQLStore} for more
 * information.  You should subclass this class if you need to create
 * an SQL store that needs to access its database using an
 * application's database abstraction layer instead of a PEAR database
 * connection.  Any subclass of Auth_OpenID_DatabaseConnection MUST
 * adhere to the interface specified here.
 *
 * @package OpenID
 */
class Neuron_Auth_MySQLConnection 
    extends Auth_OpenID_DatabaseConnection {

            private $debug = false;
            private $error = false;
    
        private function printf ($sql, $params)
        {
            $db = Neuron_DB_Database::getInstance ();
            $sql = str_replace ('?', '%s', $sql);
            $sql = str_replace ('!', '%s', $sql);

            foreach ($params as $k => $v)
            {
                if (is_numeric ($v))
                {
                    $params[$k] = $v;
                }
                else
                {
                    $params[$k] = "'" . $db->escape ($v) . "'";
                }
            }

            $sql = vsprintf ($sql, $params);
            return $sql;
        }

    /**
     * Sets auto-commit mode on this database connection.
     *
     * @param bool $mode True if auto-commit is to be used; false if
     * not.
     */
    function autoCommit($mode)
    {
    }

    /**
     * Run an SQL query with the specified parameters, if any.
     *
     * @param string $sql An SQL string with placeholders.  The
     * placeholders are assumed to be specific to the database engine
     * for this connection.
     *
     * @param array $params An array of parameters to insert into the
     * SQL string using this connection's escaping mechanism.
     *
     * @return mixed $result The result of calling this connection's
     * internal query function.  The type of result depends on the
     * underlying database engine.  This method is usually used when
     * the result of a query is not important, like a DDL query.
     */
    function query($sql, $params = array())
    {
        $db = Neuron_DB_Database::getInstance ();
        $sql = $this->printf ($sql, $params);

        if ($this->debug)
        {
            echo $sql . "<br><br>";
        }

        try
        {
            //echo $sql . "<br><br>";

            $data = $db->query ($sql);

            if ($this->debug)
            {
                echo '<pre>';
                var_dump ($data);
                echo "</pre><br><br>";
            }

            $this->error = false;
            return $data;
        }
        catch (Exception $e)
        {
            $this->error = true;
            echo 'error';
        }
    }

    /**
     * Starts a transaction on this connection, if supported.
     */
    function begin()
    {
    }

    /**
     * Commits a transaction on this connection, if supported.
     */
    function commit()
    {
    }

    /**
     * Performs a rollback on this connection, if supported.
     */
    function rollback()
    {
    }

    /**
     * Run an SQL query and return the first column of the first row
     * of the result set, if any.
     *
     * @param string $sql An SQL string with placeholders.  The
     * placeholders are assumed to be specific to the database engine
     * for this connection.
     *
     * @param array $params An array of parameters to insert into the
     * SQL string using this connection's escaping mechanism.
     *
     * @return mixed $result The value of the first column of the
     * first row of the result set.  False if no such result was
     * found.
     */
    function getOne($sql, $params = array())
    {
        //echo 'get one --- ';

        $data = $this->query ($sql, $params);

        if (count ($data) > 0)
        {
            $data = array_values ($data[0]);
            return $data[0];
        }
        return false;
    }

    /**
     * Run an SQL query and return the first row of the result set, if
     * any.
     *
     * @param string $sql An SQL string with placeholders.  The
     * placeholders are assumed to be specific to the database engine
     * for this connection.
     *
     * @param array $params An array of parameters to insert into the
     * SQL string using this connection's escaping mechanism.
     *
     * @return array $result The first row of the result set, if any,
     * keyed on column name.  False if no such result was found.
     */
    function getRow($sql, $params = array())
    {
        //echo 'get row --- ';

        $data = $this->query ($sql, $params);

        $row = false;
        if (count ($data) > 0)
        {
            $row = $data[0];
        }

        //var_dump ($row);
        //echo '<br><br>';

        return $row;
    }

    /**
     * Run an SQL query with the specified parameters, if any.
     *
     * @param string $sql An SQL string with placeholders.  The
     * placeholders are assumed to be specific to the database engine
     * for this connection.
     *
     * @param array $params An array of parameters to insert into the
     * SQL string using this connection's escaping mechanism.
     *
     * @return array $result An array of arrays representing the
     * result of the query; each array is keyed on column name.
     */
    function getAll($sql, $params = array())
    {
        //echo 'get all --- ';

        $data = $this->query ($sql, $params);
        return $data;
    }

    function isError ($value)
    {
        return $this->error;
    }
}

