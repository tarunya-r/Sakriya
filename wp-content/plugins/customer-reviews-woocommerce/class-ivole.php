<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once('class-ivole-admin.php');
require_once('class-ivole-admin-coupon.php');
require_once('class-ivole-sender.php');
require_once('class-ivole-reviews.php');
require_once('class-ivole-endpoint.php');
require_once('class-ivole-reporter.php');
require_once('class-ivole-manual.php');
require_once('class-ivole-structured-data.php');

class Ivole {
  public function __construct() {
    $ivole_admin = new Ivole_Admin();
    $ivole_admin_coupon = new Ivole_Admin_Coupon();
		$ivole_sender = new Ivole_Sender();
		$ivole_reviews = new Ivole_Reviews();
		$ivole_endpoint = new Ivole_Endpoint();
		$ivole_reporter = new Ivole_Reporter();
		$ivole_manual = new Ivole_Manual();
		$ivole_structured_data = new Ivole_StructuredData();
  }
}

?>
