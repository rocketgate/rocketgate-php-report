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
 
//////////////////////////////////////////////////////////////////////
//
//	Declaration of static response codes.
//
//////////////////////////////////////////////////////////////////////
//
define("ReportCodes__RESPONSE_SUCCESS", 0);	// Function succeeded
define("ReportCodes__RESPONSE_SYSTEM_ERROR", 3);
						// Server/recoverable error
define("ReportCodes__RESPONSE_REQUEST_ERROR", 4);
						// Invalid request

//////////////////////////////////////////////////////////////////////
//
//	Declaration of static reason codes.
//
//////////////////////////////////////////////////////////////////////
//
define("ReportCodes__REASON_SUCCESS", 0);	// Function succeeded

define("ReportCodes__REASON_DNS_FAILURE", 300);
define("ReportCodes__REASON_UNABLE_TO_CONNECT", 301);
define("ReportCodes__REASON_REQUEST_XMIT_ERROR", 302);
define("ReportCodes__REASON_RESPONSE_READ_TIMEOUT", 303);
define("ReportCodes__REASON_RESPONSE_READ_ERROR", 304);
define("ReportCodes__REASON_SERVICE_UNAVAILABLE", 305);
define("ReportCodes__REASON_CONNECTION_UNAVAILABLE", 306);
define("ReportCodes__REASON_BUGCHECK", 307);
define("ReportCodes__REASON_UNHANDLED_EXCEPTION", 308);
define("ReportCodes__REASON_SQL_EXCEPTION", 309);

define("ReportCodes__REASON_XML_ERROR", 400);
define("ReportCodes__REASON_INVALID_URL", 401);
define("ReportCodes__REASON_INVALID_MERCHANT_ID", 406);
define("ReportCodes__REASON_INVALID_ACCESS_CODE", 411);
define("ReportCodes__REASON_INVALID_CUSTOMER_ID", 414);

?>
