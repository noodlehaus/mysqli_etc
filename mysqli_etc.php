<?php declare(strict_types=1);

# Executes an insert and returns the insert id if any.
function mysqli_insert($db, string $sql, ...$params): string {

  $stmt = mysqli_interpolate($db, $sql, ...$params);

  if (!mysqli_stmt_execute($stmt)) {
    throw new mysqli_sql_exception(
      mysqli_stmt_error($stmt),
      mysqli_stmt_errno($stmt)
    );
  }

  $id = mysqli_insert_id($db);
  mysqli_stmt_close($stmt);

  return (string) $id;
}

# Executes a select and returns all resulting rows
function mysqli_select($db, string $sql, ...$params): array {

  $stmt = mysqli_interpolate($db, $sql, ...$params);

  if (
    !mysqli_stmt_execute($stmt) ||
    (false === ($result = mysqli_stmt_get_result($stmt)))
  ) {
    throw new mysqli_sql_exception(
      mysqli_stmt_error($stmt),
      mysqli_stmt_errno($stmt)
    );
  }

  $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

  mysqli_free_result($result);
  mysqli_stmt_close($stmt);

  return $rows;
}

# Executes an update/delete query and returns affected row count.
function mysqli_update($db, string $sql, ...$params): int {

  $stmt = mysqli_interpolate($db, $sql, ...$params);

  if (!mysqli_stmt_execute($stmt)) {
    throw new mysqli_sql_exception(
      mysqli_stmt_error($stmt),
      mysqli_stmt_errno($stmt)
    );
  }

  $affected = mysqli_stmt_affected_rows($stmt);
  mysqli_stmt_close($stmt);

  return $affected;
}

# Expansion of mysqli_prepare
function mysqli_interpolate($db, string $sql, ...$args): mysqli_stmt {

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

    if (false === mysqli_stmt_bind_param($stmt, $syms, ...$refs)) {
      throw new mysqli_sql_exception(
        mysqli_stmt_error($stmt),
        mysqli_stmt_errno($stmt)
      );
    }
  }

  return $stmt;
}
