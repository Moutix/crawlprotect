<?php

require "prelude.php";

function IdmefLogging($type, $content, $analyzer_name)
{
  require "../language/english.php";

  $client = new client($analyzer_name, 4, "Crawlprotect", "WAF", "http://www.crawltrack.fr/crawlprotect");

  $client->start();

  $idmef = new idmef();

  $idmef->set("alert.classification.text", $language[$type]);
  $idmef->set("alert.source(0).node.address(0).address", $_SERVER['REMOTE_ADDR']);
  $idmef->set("alert.source(0).node.address(0).category", "ipv4-addr");
  $idmef->set("alert.target(0).service.web_service.url", $content);

  #Extraction des autres informations de SERVER
  $idmef->set("alert.target(0).service.web_service.http_method", $_SERVER['REQUEST_METHOD']);
  $idmef->set("alert.target(0).node.address(0).address", $_SERVER['SERVER_ADDR']);
  $idmef->set("alert.target(0).node.address(0).category", "ipv4-addr");
  $idmef->set("alert.target(0).service.protocol", $_SERVER['SERVER_PROTOCOL']);
  $idmef->set("alert.target(0).service.port", $_SERVER['SERVER_PORT']);

  $idmef->set("alert.source(0).service.port", $_SERVER['REMOTE_PORT']);

  $idmef->set("alert.additional_data(0).type", "string");
  $idmef->set("alert.additional_data(0).meaning", "User Agent");
  $idmef->set("alert.additional_data(0).data", $_SERVER['HTTP_USER_AGENT']);

  if($_SERVER['HTTP_REFERER']){
  $idmef->set("alert.additional_data(1).type", "string");
  $idmef->set("alert.additional_data(1).meaning", "Referer");
  $idmef->set("alert.additional_data(1).data", $_SERVER['HTTP_REFERER']);
  }

  $idmef->set("alert.assessment.impact.severity", "low");

  $client->sendIDMEF($idmef);
  return;
 }

?>
