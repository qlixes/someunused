#!/bin/sh
curl -d "action=login&data=eyJhcGlfa2V5IjoiTXpjNFpqTTRZekV6WlRRNFlqTTJaV05rTTJJd1lqZzRZV1V6TWpReFlUTTBaalpsTVRnME5RPT1bIiwidXNlcm5hbWUiOiJhZG1pbiIsInBhc3N3b3JkIjoidHJpYWwiLCJsb25naXR1ZGUiOiIwIiwibGF0aXR1ZGUiOiIwIn0=" $1
curl -d '{"action":"login","data":"eyJhcGlfa2V5IjoiTXpjNFpqTTRZekV6WlRRNFlqTTJaV05rTTJJd1lqZzRZV1V6TWpReFlUTTBaalpsTVRnME5RPT1bIiwidXNlcm5hbWUiOiJhZG1pbiIsInBhc3N3b3JkIjoidHJpYWwiLCJsb25naXR1ZGUiOiIwIiwibGF0aXR1ZGUiOiIwIn0="}' -H 'Content-Type:application/json' $1
curl -d "{'action':'login','data':'eyJhcGlfa2V5IjoiTXpjNFpqTTRZekV6WlRRNFlqTTJaV05rTTJJd1lqZzRZV1V6TWpReFlUTTBaalpsTVRnME5RPT1bIiwidXNlcm5hbWUiOiJhZG1pbiIsInBhc3N3b3JkIjoidHJpYWwiLCJsb25naXR1ZGUiOiIwIiwibGF0aXR1ZGUiOiIwIn0='}" -H "Content-Type:application/json" $1
