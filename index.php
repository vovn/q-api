<?php

require_once __DIR__.'/vendor/autoload.php';

$app = new Silex\Application();

$api['debug'] = true;

// allow cross domain requests
$headers['Access-Control-Allow-Origin'] = '*';

// get all jobs
$app->get('/', function () use ($app, $headers) {
  $db = new PDO("sqlsrv:Server=vo-sql;Database=NGProd", 'sa', 'vmed$'); 
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $qry = $db->query("SELECT u.user_id, u.last_name+', '+u.first_name user_name, q.last_name+', '+q.first_name patient_name, q.template_file document_name, q.status, CONVERT(CHAR(23), q.modify_timestamp, 126) as iso8601_timestamp, CONVERT(VARCHAR(19),q.modify_timestamp) as human_timestamp, REPLACE(REPLACE(REPLACE(REPLACE(CONVERT(CHAR(23), q.modify_timestamp, 126),'-',''),'T',''),':',''),'.','') as sort, q.priority, q.send_to_machine server FROM doc_queue_pend q JOIN user_mstr u on u.user_id = q.created_by ORDER BY q.modify_timestamp DESC");
  $qry->setFetchMode(PDO::FETCH_ASSOC);

  $rows = $qry->fetchAll();

  return $app->json($rows, 200, $headers);
});

// get specific document
// $app->get('/{doc_id}', function ($doc_id) use ($app) {
//   return 'Hello '.$app->escape($doc_id);
// });

$app->run();