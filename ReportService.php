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
require_once("ReportCodes.php");
require_once("ReportRequest.php");
require_once("ReportResponse.php");

////////////////////////////////////////////////////////////////////////////////
//
//	ReportService() - Object that performs sends transactions
//			  to a RocketGate Report Server.
//				    
////////////////////////////////////////////////////////////////////////////////
//
class ReportService {
  var $rocketGateHost;			// Report hostname
  var $rocketGateProtocol;		// Message protocol
  var $rocketGatePortNo;		// Network connection port
  var $rocketGateServlet;		// Destination servlet
  var $rocketGateConnectTimeout;	// Timeout for network connection
  var $rocketGateReadTimeout;		// Timeout for network read


//////////////////////////////////////////////////////////////////////
//
//	ReportService() - Constructor for class.
//
//////////////////////////////////////////////////////////////////////
//
  function __construct()
  {
//
//	Set the standard production destinations for the
//	service.
//
    $this->SetTestMode(FALSE);			// Assume production mode
    $this->rocketGateServlet = "reports/servlet/ReportServiceAccess";
    $this->rocketGateConnectTimeout = 10;	// 10 second connection timeout
    $this->rocketGateReadTimeout = 600;		// 10 minute operation timeout
  }


//////////////////////////////////////////////////////////////////////
//
//	SetTestMode() - Set the communications parameters for
//			production or test mode.
//
//////////////////////////////////////////////////////////////////////
//
  function SetTestMode($testFlag)
  {
//
//	If the test flag is set, use the test setup parameters.
//
    if ($testFlag) {				// In test mode?
      $this->rocketGateHost = "dev-gateway.rocketgate.com";
      $this->rocketGateProtocol = "https";	// Use SSL
      $this->rocketGatePortNo = "443";		// SSL port
    
//
//	If the test flag is not set, use the production parameters.
//
    } else {
      $this->rocketGateHost = "gateway.rocketgate.com";
      $this->rocketGateProtocol = "https";	// Use SSL
      $this->rocketGatePortNo = "443";		// SSL port
    }
  }


//////////////////////////////////////////////////////////////////////
//
//	SetHost() - Set the host used by the service.
//
//////////////////////////////////////////////////////////////////////
//
  function SetHost($hostname)
  {
    $this->rocketGateHost = $hostname;		// Use this hostname
  }


//////////////////////////////////////////////////////////////////////
//
//	SetProtocol() - Set the communications protocol used by
//			the service.
//
//////////////////////////////////////////////////////////////////////
//
  function SetProtocol($protocol)
  {
    $this->rocketGateProtocol = $protocol;	// HTTP, HTTPS, etc.
  }


//////////////////////////////////////////////////////////////////////
//
//	SetPortNo() - Set the port number used by the service.
//
//////////////////////////////////////////////////////////////////////
//
  function SetPortNo($portNo)
  {
    $this->rocketGatePortNo = $portNo;		// IP port
  }


//////////////////////////////////////////////////////////////////////
//
//	SetServlet() - Set the servlet used by the service.
//
//////////////////////////////////////////////////////////////////////
//
  function SetServlet($servlet)
  {
    $this->rocketGateServlet = $servlet;	// Tomcat servlet
  }


//////////////////////////////////////////////////////////////////////
//
//	SetConnectTimouet() - Set the timeout used during connection
//			      to the servlet.
//
//////////////////////////////////////////////////////////////////////
//
  function SetConnectTimeout($timeout)
  {
    $this->rocketGateConnectTimeout = $timeout;	// Number of seconds
  }


//////////////////////////////////////////////////////////////////////
//
//	SetReadTimouet() - Set the timeout used while waiting for
//			   the servlet to answer.
//
//////////////////////////////////////////////////////////////////////
//
  function SetReadTimeout($timeout)
  {
    $this->rocketGateReadTimeout = $timeout;	// Number of seconds
  }


//////////////////////////////////////////////////////////////////////
//
//	GenerateReport() - Generate a report outlined in a
//			   ReportRequest.
//
//////////////////////////////////////////////////////////////////////
//
  function GenerateReport($request, $response)
  {
//
//	If the request specifies a server name, use it.
//	Otherwise, use the default for the service.
//
    $serverName = $request->Get("reportServer");
    if ($serverName == NULL) $serverName = $this->rocketGateHost;

//
//	Lookup the hostname in DNS.
//
    if (strcmp($serverName, "gw.rocketgate.com") != 0) {
      $hostList = array();                      // Create an array
      $hostList[0] = $serverName;               // Use name directly
    } else {
      $hostList = gethostbynamel($serverName);	// Lookup the hostname
      if (!($hostList)) {			// Lookup failed?
        $hostList = array();			// Create an array
        $hostList[0] = "gateway-16.rocketgate.com";	// Add default resolution
        $hostList[1] = "gateway-17.rocketgate.com";
      } else {
        $index = 0;                             // Initialize index
        $listSize = count($hostList);           // Get element count
        while ($index < $listSize) {            // Loop over all entries
          if (strcmp($hostList[$index], "69.20.127.91") == 0)
            $hostList[$index] = "gateway-16.rocketgate.com";
          if (strcmp($hostList[$index], "72.32.126.131") == 0)
            $hostList[$index] = "gateway-17.rocketgate.com";
          $index++;                             // Look at next in list
        }
      }
    }

//
//	Randomly select an end-point to use first.
//
    if (($listSize = count($hostList)) > 1) {	// More than one address?
      $index = rand(0, ($listSize - 1));	// Get random index
      if ($index > 0) {				// Want to swap?
	$swapper = $hostList[0];		// Save this one
	$hostList[0] = $hostList[$index];	// Put this one first
	$hostList[$index] = $swapper;		// And put this one here
      }
    }

//
//	Loop over the hosts in the DNS entry.  Try to send the
//	transaction to each host until it finally succeeds.  If it
//	fails due to an unrecoverable system error, we must quit.
//
    $index = 0;					// Start with first entry
    while ($index < $listSize) {		// Loop over all entries
      $results = $this->PerformCURLTransaction($hostList[$index],
					       $request,
					       $response);
      if ($results == ReportCodes__RESPONSE_SUCCESS) return TRUE;
      if ($results != ReportCodes__RESPONSE_SYSTEM_ERROR) return FALSE;
      $index++;					// Try next host in list
    }
    return FALSE;				// Transaction failed
  }


//////////////////////////////////////////////////////////////////////
//
//	PerformCURLTransaction() - Perform a transaction exchange
//				   with a given host.
//
//////////////////////////////////////////////////////////////////////
//
  function PerformCURLTransaction($host, $request, $response)
  {
//
//	Reset the response object and turn the request into
//	a string that can be transmitted.
//
    $response->Reset();				// Clear old contents
    $requestBytes = $request->ToXMLString();	// Change to XML request

//
//	Gather override attibutes used for the connection URL.
//
    $urlServlet = $request->Get("reportServlet");
    $urlProtocol = $request->Get("reportProtocol");
    $urlPortNo = $request->Get("reportPortNo");

//
//	If the parameters were not set in the request,
//	use the system defaults.
//
    if ($urlServlet == NULL) $urlServlet = $this->rocketGateServlet;
    if ($urlProtocol == NULL) $urlProtocol = $this->rocketGateProtocol;
    if ($urlPortNo == NULL) $urlPortNo = $this->rocketGatePortNo;

//
//	Build the URL for the gateway service.
//
    $url = $urlProtocol . "://" 		// Start with protocol
			. $host	. ":"		// Add the host
			. $urlPortNo . "/"	// Add the port number
			. $urlServlet;		// Add servlet path
//
//	Gather the override timeout values that will be used
//	for the connection.
//
    $connectTimeout = $request->Get("reportConnectTimeout");
    $readTimeout = $request->Get("reportReadTimeout");

//
//	Use default values if the parameters were not set.
//
    if ($connectTimeout == NULL)		// No connect timeout specified?
      $connectTimeout = $this->rocketGateConnectTimeout;
    if ($readTimeout == NULL) $readTimeout = $this->rocketGateReadTimeout;
 
//
//	Create a handle that can be used for the URL operation.
//
    if (!($handle = curl_init())) {		// Failed to initialize?
      $response->Set(ReportResponse::EXCEPTION(), "curl_init() error");
      $response->SetResults(ReportCodes__RESPONSE_REQUEST_ERROR,
			    ReportCodes__REASON_INVALID_URL);
      return ReportCodes__RESPONSE_REQUEST_ERROR;
    }

//
//	Set timeout values used in the operation.
//
    curl_setopt($handle, CURLOPT_NOSIGNAL, TRUE);
    curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, $connectTimeout);
    curl_setopt($handle, CURLOPT_TIMEOUT, $readTimeout);

//
//	Setup verification for SSL connections.
//
    curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, FALSE);

//
//	Setup the call to the URL.
//
    curl_setopt($handle, CURLOPT_POST, TRUE);
    curl_setopt($handle, CURLOPT_POSTFIELDS, $requestBytes);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($handle, CURLOPT_URL, $url);
    curl_setopt($handle, CURLOPT_FAILONERROR, TRUE);
    curl_setopt($handle, CURLOPT_USERAGENT, "RG PHP Report Client PR2.0");
    curl_setopt ($handle, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));

//
//	Execute the operation.
//
    $results = curl_exec($handle);		// Execute the operation
    if (!($results)) {				// Did it fail?
      $errorCode = curl_errno($handle);		// Get the error code
      $errorString = curl_error($handle);	// Get the error text
      curl_close($handle);			// Done with handle

//
//	Translate the CURL error code into a Gateway code.
//
      switch($errorCode) {			// Classify error code
        case CURLE_SSL_CONNECT_ERROR:		// Connection failures
	case CURLE_COULDNT_CONNECT:
          $internalCode = ReportCodes__REASON_UNABLE_TO_CONNECT;
	  break;				// Done with request
        case CURLE_SEND_ERROR:			// Failed sending data
          $internalCode = ReportCodes__REASON_REQUEST_XMIT_ERROR;
	  break;				// Done with request
        case CURLE_OPERATION_TIMEOUTED:		// Time-out reached
          $internalCode = ReportCodes__REASON_RESPONSE_READ_TIMEOUT;
	  break;				// Done with request
        case CURLE_RECV_ERROR:			// Failed reading data
        case CURLE_READ_ERROR:
        default:
	  $internalCode = ReportCodes__REASON_RESPONSE_READ_ERROR;
      }

//
//	If the operation failed, return an error code.
//
      if (strlen($errorString) != 0)		// Have an error?
        $response->Set(ReportResponse::EXCEPTION(), $errorString);
      $response->SetResults(ReportCodes__RESPONSE_SYSTEM_ERROR,
			    $internalCode);
      return ReportCodes__RESPONSE_SYSTEM_ERROR;
    }

//
//	Parse the returned message into the response
//	object.
//
    curl_close($handle);			// Done with handle
    $response->SetFromXML($results);		// Set response
    return $response->Get(ReportResponse::RESPONSE_CODE());
  }
}
?>
