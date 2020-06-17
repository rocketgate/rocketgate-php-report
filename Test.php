<?php
/*
 * Copyright notice:
 * (c) Copyright 2007-2012 RocketGate LLC
 * All rights reserved.
 *
 * The copyright notice must not be removed without specific, prior
 * written permission from RocketGate LLC.
 *
 * This software is protected as an unpublished work under the U.S. copyright
 * laws. The above copyright notice is not intended to effect a publication of
 * this work.
 * This software is the confidential and proprietary information of RocketGate LLC.
 * Neither the binaries nor the source code may be redistributed without prior
 * written permission from RocketGate LLC.
 *
 * The software is provided "as-is" and without warranty of any kind, express, implied
 * or otherwise, including without limitation, any warranty of merchantability or fitness
 * for a particular purpose.  In no event shall RocketGate LLC be liable for any direct,
 * special, incidental, indirect, consequential or other damages of any kind, or any damages
 * whatsoever arising out of or in connection with the use or performance of this software,
 * including, without limitation, damages resulting from loss of use, data or profits, and
 * whether or not advised of the possibility of damage, regardless of the theory of liability.
 * 
 */
include("ReportService.php");

//
//	Allocate the objects we need for the test.
//
$request = new ReportRequest();
$response = new ReportResponse();
$service = new ReportService();

//
//	Setup the report request.
//
$request->Set(ReportRequest::MERCHANT_ID(), "1");
$request->Set(ReportRequest::MERCHANT_PASSWORD(), "testpassword");
$request->Set(ReportRequest::MERCHANT_CUSTOMER_ID(), "Customer-1");
$request->Set(ReportRequest::REPORT_NAME(), "paymentInfoList");

//
//	Setup test parameters in the service and
//	request.
//
$service->SetTestMode(TRUE);

//
//	Generate the report.
//
if ($service->GenerateReport($request, $response)) {
  print "Report succeeded\n";
  print "Response Code: " .
	$response->Get(ReportResponse::RESPONSE_CODE()) . "\n";
  print "Reasone Code: " .
	$response->Get(ReportResponse::REASON_CODE()) . "\n";
  print "Report: " . $response->Get(ReportResponse::REPORT_PAYLOAD());
} else {
  print "Report failed\n";
  print "Response Code: " .
	$response->Get(ReportResponse::RESPONSE_CODE()) . "\n";
  print "Reasone Code: " .
	$response->Get(ReportResponse::REASON_CODE()) . "\n";
  print "Exception: " .
	$response->Get(ReportResponse::EXCEPTION()) . "\n";
}


?>
