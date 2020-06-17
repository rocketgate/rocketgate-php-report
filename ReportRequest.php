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
require_once("ReportParameterList.php");

////////////////////////////////////////////////////////////////////////////////
//
//	ReportRequest() - Object that holds name-value pairs
//			  that describe a gateway request.
//				    
////////////////////////////////////////////////////////////////////////////////
//
class ReportRequest extends ReportParameterList {

//////////////////////////////////////////////////////////////////////
//
//	ReportRequest() - Constructor for class.
//
//////////////////////////////////////////////////////////////////////
//
  function ReportRequest()
  {
//
//	Initialize the request list.
//
    ReportParameterList::ReportParameterList();
    $this->Set(ReportRequest::VERSION_INDICATOR(), "PR2.0");
  }


//////////////////////////////////////////////////////////////////////
//
//	ToXMLString() - Transform the parameter list into
//			an XML String.
//
//////////////////////////////////////////////////////////////////////
//
  function ToXMLString()
  {

//
//	Build the header of XML document.
//
    $xmlString = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" .
		 "<reportRequest>";

//
//	Loop over the list of values in the parameter list.
//
    foreach ($this->params as $key => $value) {
      $xmlString .= "<" . $key . ">";		// Add opening of element
      $xmlString .= $this->TranslateXML($value);
      $xmlString .= "</" . $key . ">";		// Add closing of element
    }

//
//	Put the closing marker on the XML document and quit.
// 
    $xmlString .= "</reportRequest>";		// Add the terminator
    return $xmlString;				// Return completed XML
  }


//////////////////////////////////////////////////////////////////////
//
//	TranslateXML() - Translate a string to a valid XML
//			 string that can be used in an attribute
//			 or text node.
//
//////////////////////////////////////////////////////////////////////
//
  function TranslateXML($sourceString)
  {
    $sourceString = str_replace("&", "&amp;", $sourceString);
    $sourceString = str_replace("<", "&lt;", $sourceString);
    $sourceString = str_replace(">", "&gt;", $sourceString);
    return $sourceString;			// Give back results
  }


//////////////////////////////////////////////////////////////////////
//
//	Functions that provide constants for name-value pairs.
//
//////////////////////////////////////////////////////////////////////
//
  static function VERSION_INDICATOR() { return "version"; }

  static function MERCHANT_CUSTOMER_ID() { return "merchantCustomerID"; }
  static function MERCHANT_ID() { return "merchantID"; }
  static function MERCHANT_PASSWORD() { return "merchantPassword"; }
  static function MERCHANT_SITE_ID() { return "merchantSiteID"; }
  static function REPORT_NAME() { return "reportName"; }

//////////////////////////////////////////////////////////////////////
//
//	Functions used to override gateway service URL.
//
//////////////////////////////////////////////////////////////////////
//
  static function REEPORT_SERVER() { return "reportServer"; }
  static function REEPORT_PROTOCOL() { return "reportProtocol"; }
  static function REEPORT_PORTNO() { return "reportPortNo"; }
  static function REEPORT_SERVLET() { return "reportServlet"; }
  static function REEPORT_CONNECT_TIMEOUT() { return "reportConnectTimeout"; }
  static function REEPORT_READ_TIMEOUT() { return "reportReadTimeout"; }
}

?>
