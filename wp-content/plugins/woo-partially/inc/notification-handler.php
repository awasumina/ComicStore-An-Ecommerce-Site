<?php
if( ! defined('ABSPATH') ) die('Not Allowed');

add_action('parse_request', 'checkPartiallyNotificationUrl');

function checkPartiallyNotificationUrl() {
  $path = $_SERVER['REQUEST_URI'];
  if (strpos($path, WC_Gateway_Partially::NOTIFICATION_PATH) !== false) {
    $gateway = WC_Gateway_Partially::instance();
    $gateway->handlePartiallyNotification();
    exit();
  }
}
