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
require_once("ReportParameterList.php");

////////////////////////////////////////////////////////////////////////////////
//
//	ReportResponse() - Object that holds name-value pairs
//			   that describe a gateway response.
//				    
////////////////////////////////////////////////////////////////////////////////
//
class ReportResponse extends ReportParameterList {

//////////////////////////////////////////////////////////////////////
//
//	ReportResponse() - Constructor for class.
//
//////////////////////////////////////////////////////////////////////
//
  function __construct()
  {
//
//	Initialize the parameter list.
//
    ReportParameterList::__construct();
  }


//////////////////////////////////////////////////////////////////////
//
//	SetResults() - Set the response and reason values.
//
//////////////////////////////////////////////////////////////////////
//
  function SetResults($response, $reason)
  {
    $this->Set(ReportResponse::RESPONSE_CODE(), $response);
    $this->Set(ReportResponse::REASON_CODE(), $reason);
  }


//////////////////////////////////////////////////////////////////////
//
//	SetFromXML() - Set the internal parameters using
//		       the contents of an XML document.
//
//////////////////////////////////////////////////////////////////////
//
  function SetFromXML($xmlString)
  {
//
//	Turn the string into an XML document.
//
    $doc = new DOMDocument();			// Empty document
    $doc->loadXML($xmlString);			// Load from XML

//
//	Get all of the top-level nodes.
//
    $rootNode = $doc->documentElement;		// Get the document
    $nodeList = $rootNode->childNodes;		// Get children at top

//
//	Loop over the nodes.  If we see the report payload,
//	save the XML.
//
    foreach ($nodeList as $node) {		// Loop over nodes
      if ($node->nodeName != "reportPayload") {	// Not the payload?
	$this->Set($node->nodeName, $node->nodeValue);
      } else {
	$this->Set($node->nodeName, $doc->saveXML($node));
      }
    }
  }


//////////////////////////////////////////////////////////////////////
//
//	Functions that provide constants for name-value pairs.
//
//////////////////////////////////////////////////////////////////////
//
  static function VERSION_INDICATOR() { return "version"; }
  static function EXCEPTION() { return "exception"; }
  static function REASON_CODE() { return "reasonCode"; }
  static function RESPONSE_CODE() { return "responseCode"; }
  static function REPORT_PAYLOAD () { return "reportPayload"; }
}

?>
