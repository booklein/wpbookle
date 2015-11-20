<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pay with Amazon
 *
 * The PWA MWS Report class use MWS Report API to update order details.
 *
 * @class 		PWA_Mws_Report
 * @version		1.0.0
 * @package		PWA/Mws_report
 * @category	Class
 * @author 		Amazon
 */
 
 
class PWA_Mws_Report extends PWA{
	
	
	
	/**
	 * Constructor for the PWA MWS Report class.
	 */
	public function __construct() {
		$this->includes();
	}
	
	
	/**
	 * Include required core files and classes.
	 */
	public function includes() {
		require_once('ManageReportSchedule.php');
		require_once('GetReportRequestList.php');
		require_once('GetReportList.php');
		require_once('GetReport.php');
		require_once('SubmitFeed.php');
	}
	
	/*
	 * Schedule Reports on seller central
	 */
	public function schedule_report_api() {
		$report_schedule = new Manage_Report_Schedule();
		$report_schedule->init();
		exit;
	}
	
	
	/*
	 * Fetch Report Request Lists from seller central
	 */
	public function get_report_request_list_api() {
		$report_request_list = new Get_Report_Request_List();
		$report_request_list->init();
		exit;
	}
	
	
	/*
	 * Fetch Reports from seller central
	 */
	public function get_report_list_api($ReportRequestId) {
		$report_list = new Get_Report_List();
		return $report_list->init($ReportRequestId);
	}
	
	
	/*
	 * Fetch Reports from seller central
	 */
	public function get_report_api($ReportId) {
		$report = new Get_Report();
		return $report->init($ReportId);
	}
	
	/*
	 * Feed API to acknowledge an order on seller central
	 */
	public function submit_acknowledge_feed($param) {
		$submit_feed = new Submit_Feed();
		$submit_feed->acknowledge_feed($param);
	}
	
	/*
	 * Feed API to cancel an order on seller central
	 */
	public function submit_cancel_feed($param) {
		$submit_feed = new Submit_Feed();
		$submit_feed->cancel_feed($param);
	}
	
	/*
	 * Feed API to refund for an order on seller central
	 */
	public function submit_refund_feed($param) {
		$submit_feed = new Submit_Feed();
		$submit_feed->refund_feed($param);
	}
	
	
	
	
	
	
	
}
