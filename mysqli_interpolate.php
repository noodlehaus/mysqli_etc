<?php
/**
 * mysqli_interpolate() is like sprintf() for mysql
 * prepared statements.
 *
 * @author Jesus A. Domingo <jesus.domingo@gmail.com>
 * @license MIT <http://noodlehaus.mit-license.org>
 */

/**
 * Create a prepared statement by interpolating values
 * into the statement.
 *
 * @param resource $db  The mysqli connection
 * @param string   $sql The sql statement to prepare
 * @param mixed    ...  The parameter values
 *
 * @return mysqli_stmt The prepared statement
 */
function mysqli_interpolate($db, $sql, ...$args) {

  $argn = count($args);
  $stmt = mysqli_prepare($db, $sql);

  if ($stmt === false) {
    throw new mysqli_sql_exception(mysqli_error($db), mysqli_errno($db));
  }

  if ($argn) {

    $syms = implode('', array_pad([], $argn, 's'));
    $refs = [];

    foreach ($args as $key => $val) {
      $refs[$key] = &$args[$key];
    }

    array_unshift($refs, $stmt, $syms);

    if (false === call_user_func_array('mysqli_stmt_bind_param', $refs)) {
      throw new mysqli_sql_exception(
        mysqli_stmt_error($stmt),
        mysqli_stmt_errno($stmt)
      );
    }
  }

  return $stmt;
}
