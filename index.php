<?php
/**
 * This is a code sample based on the prompt at http://www.gofundme.com/code-sample
 * Authored by Oren Robinson (baisong) at https://github.com/baisong/demojsautocomplete
 *
 * Usage:
 *   1. Rename the file to index.php
 *   2. Place the file on any server where PHP 5.2 or higher runs.
 *   3. Access it in your browser to see the demo in action.
 *
 * Screenshots:
 *  - https://raw.githubusercontent.com/baisong/demojsautocomplete/master/screenshot1.png
 *  - https://raw.githubusercontent.com/baisong/demojsautocomplete/master/screenshot2.png
 */

/**
 * (OPTIONAL) Enter your own valid database credentials here.
 *
 * Will create a database named `restaurants` with columns `restaurant_name`, cuisine_type`.
 *
 * If unable to establish a connection, the demo will use hardcoded values.
 */
function dja_get_mysqli_info()
{
  // Required MySQL database credentials.
  $info = array(
    'host' => 'localhost',
    'username' => 'root',
    'passwd' => 'root',
    'dbname' => 'demo'
  );
  
  return $info;
}

?>
<!DOCTYPE html>
<html>
  <head>
    <title>Demo JS Autocomplete PHP Sample</title>
  </head>
  <body>
  <style type="text/css">
  body, html {
    background-color: #efefef;
    font-family: Arial, Helvetica, sans-serif;
  }
  
  .dja-wrapper {
    margin: 40px auto;
    border-radius: 2px;
    box-shadow: 2px 2px 5px #888888;
    background-color: white;
    padding: 20px;
    width: 600px;
    height: 400px;
    text-align: center;
  }
  
  .ui-helper-hidden-accessible div {
    display: none;
  }
  
  .dja-wrapper h1 {
    font-weight: normal;
    color: gray;
  }
  .dja-wrapper input {
    width: 80%;
    height: 20px;
    padding: 6px 6px 5px;
    font-size: 18px;
  }
  
  ul.ui-autocomplete {
    margin: 0px !important;
    width: 80px;
    list-style-type: none;
    padding: 0px;
  }
  
  .ui-autocomplete li {
    padding: 10px 0px;
    width: 100%;
    margin: 0px !important;
  }
  
  li.ui-state-focus {
    background-color: blue;
    color: white;
  }
  
  </style>
    <div class="dja-wrapper">
		  <h1>Demo JS Autocomplete PHP Sample</h1>
		  <div id="dja-search">
        <input id="dja-input" type="text" name="dja-input" placeholder="Enter restaurant name or cuisine...">
	  </div>
	  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	  <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js"></script>
	  <script type="text/javascript">
	  var names = [
	  <?php
$restaurants = dja_get_restaurants();
foreach ($restaurants as $row) {
  echo '"' . $row['restaurant_name'] . '", ';
}
?>
	  ];
	  var cuisines = [
	  <?php
foreach ($restaurants as $row) {
  echo '"' . $row['cuisine_type'] . '", ';
}
?>
	  ];
	  var display_names = [
	  <?php
foreach ($restaurants as $row) {
  echo '"' . $row['restaurant_name'] . ' (' . $row['cuisine_type'] . ')", ';
}
?>
	  ];
    $("#dja-input").autocomplete({ source: function( request, response ) {
      // A RegExp object that matches only a string starting with the value.
      var matcher = new RegExp("^" + $.ui.autocomplete.escapeRegex( request.term ), "i");
      response($.grep(display_names, function(item, i) {
        return matcher.test(names[i]) || matcher.test(cuisines[i]);
      }));
    }});
    </script>
  </body>
</html>
<?php

/**
 * Loads the restaurants either from a MySQL database, or a hardcoded fallback.
 */
function dja_get_restaurants()
{
  // Default hardcoded values for demonstration purposes.
  $restaurants = dja_get_restaurants_default();
  
  // Optionally gets data from database.
  $info = dja_get_mysqli_info();
  if (!empty($info['host'])) {
    $verified = dja_verify_database_setup($info);
    if ($verified['db'] === TRUE && $verified['table'] === TRUE && $verified['data'] === TRUE) {
      $mysqli = new mysqli($info['host'], $info['username'], $info['passwd'], $info['dbname']);
      if (mysqli_connect_error()) {
        die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
      } else {
        $results = array();
        $query   = "SELECT restaurant_name, cuisine_type FROM restaurants LIMIT 20;";
        if ($result = $mysqli->query($query)) {
          while ($row = $result->fetch_assoc()) {
            $results[] = $row;
          }
        }
        if (count($results) > 0) {
          $restaurants = $results;
        }
      }
      $mysqli->close();
    }
  }
  
  return $restaurants;
}

/**
 * Fallback hardcoded data to allow demo without database connection.
 */
function dja_get_restaurants_default()
{
  return array(
    array(
      'restaurant_name' => 'Spicy City',
      'cuisine_type' => 'Chinese'
    ),
    array(
      'restaurant_name' => 'Tajima',
      'cuisine_type' => 'Ramen'
    ),
    array(
      'restaurant_name' => 'B H Chung',
      'cuisine_type' => 'Korean'
    ),
    array(
      'restaurant_name' => 'Nozomi',
      'cuisine_type' => 'Sushi'
    ),
    array(
      'restaurant_name' => 'Jersey Mike\'s',
      'cuisine_type' => 'Sandwiches'
    ),
    array(
      'restaurant_name' => 'Super Sergio\'s',
      'cuisine_type' => 'Tacos'
    ),
    array(
      'restaurant_name' => 'O\'Briens',
      'cuisine_type' => 'Bar Food'
    ),
    array(
      'restaurant_name' => 'Raki Raki',
      'cuisine_type' => 'Ramen'
    ),
    array(
      'restaurant_name' => 'Dumpling Inn',
      'cuisine_type' => 'Chinese'
    ),
    array(
      'restaurant_name' => 'Tapioka Express',
      'cuisine_type' => 'Coffee Shop'
    ),
    array(
      'restaurant_name' => 'Pangaea',
      'cuisine_type' => 'Coffee Shop'
    ),
    array(
      'restaurant_name' => 'Korea House',
      'cuisine_type' => 'Korean'
    )
  );
}

/**
 *
 */
function dja_verify_database_setup($info)
{
  $return = array(
    'db' => FALSE,
    'table' => FALSE,
    'data' => FALSE
  );
  
  $mysqli = new mysqli($info['host'], $info['username'], $info['passwd']);
  if (empty($info['dbname'])) {
    return $return;
  }
  
  // Tries to create database if none.
  if (!$mysqli->select_db($info['dbname'])) {
    $create_database = "CREATE DATABASE " . $info['dbname'];
    if ($mysqli->query($create_database)) {
      $return['db'] = TRUE;
    } else {
      $return['db'] = mysqli_error($mysqli);
    }
    $mysqli->close();
  }
  
  // Tries to create table if none.
  $mysqli = new mysqli($info['host'], $info['username'], $info['passwd'], $info['dbname']);
  $result = $mysqli->query("SHOW TABLES LIKE 'restaurants';");
  if (is_object($result) && $result->num_rows == 0) {
    $create_table = "CREATE TABLE restaurants 
		(
		RID INT NOT NULL AUTO_INCREMENT, 
		PRIMARY KEY(RID),
		restaurant_name CHAR(15),
		cuisine_type CHAR(15)
		)";
    if ($mysqli->query($create_table)) {
      $return['table'] = TRUE;
    } else {
      $return['table'] = mysqli_error($mysqli);
    }
  }
  
  if ($return['table'] !== TRUE) {
    $result = $mysqli->query("SHOW TABLES LIKE 'restaurants';");
    if (is_object($result) && $result->num_rows > 0) {
      $return['table'] = TRUE;
    }
  }
  
  // Tries to import data if missing.
  $query    = "SELECT restaurant_name, cuisine_type FROM restaurants LIMIT 20;";
  $result   = $mysqli->query($query);
  $num_rows = $result->num_rows;
  if ($num_rows < 12) {
    $insert_query = "INSERT INTO restaurants (restaurant_name, cuisine_type) VALUES('Spicy City','Chinese'),('Tajima','Ramen'),('B H Chung','Korean'),('Nozomi','Sushi'),('Jersey Mike\'s','Sandwiches'),('Super Sergio\'s','Tacos'),('O\'Briens','Bar Food'),('Raki Raki','Ramen'),('Dumpling Inn','Chinese'),('Tapioka Express','Coffee Shop'),('Pangaea','Coffee Shop'),('Korea House','Korean');";
    if ($mysqli->query($insert_query)) {
      $return['data'] = TRUE;
    } else {
      $return['data'] = mysqli_error($mysqli);
    }
    $mysqli->close();
  }
  return $return;
}
