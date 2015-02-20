<?php
# Convenience function for interpolating variables
# into mysql statements and creating a prepared statement
# out of it.
#
# @author Jesus A. Domingo
# @email jesus.domingo@gmail.com

# expect at least the db and query
function mysqli_interpolate($db, $sql) {

  $args = array_slice(func_get_args(), 2);

  $args_remaining = count($args);
  $args_expected = substr_count($sql, '?');

  if ($args_remaining < $args_expected) {
    trigger_error(
      "Not enough query arguments. ".
      "Expected {$param_count}, received {$args_remaining}.",
      E_USER_ERROR
    );
  }

  $statement = mysqli_prepare($db, $sql);

  if ($statement === false) {
    trigger_error(
      mysqli_error($db),
      E_USER_ERROR
    );
  }

  # we have placeholders, try to interpolate
  if ($args_expected > 0) {

    $types = implode('', array_pad([], $args_expected, 's'));
    $references = [];

    foreach ($args as $key => $val) {
      $references[$key] = &$args[$key];
    }

    array_unshift($references, $statement, $types);
    call_user_func_array('mysqli_stmt_bind_param', $references);
  }

  mysqli_stmt_execute($statement);
  return $statement;
}
