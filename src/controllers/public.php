<?php
$app->get('/index', function () use ($app) {
  $stmt = $app->db->prepare('SELECT email, password FROM users');
  $stmt->execute();
  $result = $stmt->fetchAll();
  return json_encode($result);
});

$app->get('/dashboard', function () {
  return 'dashboard';
});
