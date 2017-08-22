<?php
################################################################################
#
#    Copyright (C) 2017 Rodrigo Silva (MestreLion) <yesmiles@rodrigosilva.com>
#
#    This program is free software: you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation, either version 3 of the License, or
#    (at your option) any later version.
#
#    This program is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
#
#    You should have received a copy of the GNU General Public License
#    along with this program. If not, see <http://www.gnu.org/licenses/gpl.html>

# Scrapper for TAM

################################################################################


function scrape_TAM($pnr, $name)
{
	$url  = 'https://book.latam.com/TAM/dyn/air/servicing/retrievePNR';
	$url .= '?DIRECT_RETRIEVE=TRUE&SITE=JJBKJJBK&WDS_MARKET=BR&LANGUAGE=BR';

	# Get first passenger's last name
	$name = explode('/', $name);
	$name = urlencode($name[0]);

	$url .= "&REC_LOC=${pnr}&DIRECT_RETRIEVE_LASTNAME=${name}";

	$html = file_get_contents($url);
	if(!preg_match('/<script> var clientSideData = (?P<json>{[^;]*});/U', $html, $match))
		return array('error' => substr($html, 0, 30));
	$json = json_decode($match['json'], TRUE);

	$data = extract_TAM($json);
	$data['passageiro'] = name_TAM($html);

	return $data;
}


function extract_TAM($json)
{
	# [companhia ] => Airline company name (AZUL, GOL, TAM)
	# [ticket    ] => Ticket reservation code, ie, record locator
	# [origem    ] => Origin location, as written on ticket
	# [destino   ] => Destination location. For round-trips not the return destination!
	# [idaevolta ] => Flag for round-trip tickets. 0 or 1
	# [saida     ] => Date/time of departure, 'YYYY-MM-DD HH:mm'
	# [chegada   ] => Date/time of arrival on destination
	# [passageiro] => Passengers list, multiple names joined with ', ' (for AZUL only)
	# [voo       ] => flight code, as written on ticket
	# [milhas    ] => Miles spent, integer
	# [taxas     ] => Boarding fees, float
	# [moeda     ] => Currency (BRL, USD)

	$idaevolta = (count($json['ITINERARY_DATA']) > 1) ? 1 : 0;

	$ida        = $json['ITINERARY_DATA'][0];
	$idasaida   = $ida['LIST_SEGMENT'][0];
	$idachegada = $ida['LIST_SEGMENT'][count($ida['LIST_SEGMENT'])-1];

	$data = array();

	$data['companhia']  = 'TAM';
	$data['ticket']     = $idasaida['REC_LOC'];

	$data['origem']     = location_TAM($ida['B_LOCATION']);
	$data['destino']    = location_TAM($ida['E_LOCATION']);

	$data['idaevolta']  = $idaevolta;

	$data['saida']      = date_TAM($idasaida  ['B_DATE'], $idasaida  ['B_TIME']);
	$data['chegada']    = date_TAM($idachegada['E_DATE'], $idachegada['E_TIME']);

	$data['milhas']     = 0;
	$data['taxas']      = floatval($json['ArVal'][67]);
	$data['moeda']      = $json['ArVal'][66];

	$data['voo']        = sprintf("%s %04d", $idasaida['AIRLINE']['CODE'], $idasaida['FLIGHT_NUMBER']);

	$data['passageiro'] = '';  # In HTML, not JSON

	return $data;
}


function date_TAM($date, $hour)
{
	return date('Y-m-d', intval($date)/1000) . ' ' .
		substr($hour, 0, 2) . ':' . substr($hour, 2, 2);
}


$CITY_TAM = array(
	'CNF' => 'BELO HORIZ  CNF',  # BELO HORIZONTE CNF
	'IOS' => 'ILHEUS JORGE',     # ILHEUS
	'MAD' => 'MADRID A.SUAREZ',  # MADRID
	'GIG' => 'RIO JANEIRO GIG',  # RIO DE JANEIRO GIG
	'SDU' => 'RIO JANEIRO SDU',  # RIO DE JANEIRO SDU
);
function location_TAM($data)
{
	global $CITY_TAM;
	if(isset($CITY_TAM[$data['LOCATION_CODE']]))
		return $CITY_TAM[$data['LOCATION_CODE']];

	$location = strtoupper(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $data['CITY_NAME']));

	# Check if city has more than 1 airport
	if($data['CITY_CODE'] != $data['LOCATION_CODE'])
		$location .= ' ' . $data['LOCATION_CODE'];

	return $location;
}

$NAME_TAM = array(
	'Sr'   => 'MR',
	'Sra'  => 'MRS',
	'Srta' => 'MS'  # Also 'MISS'
);
function name_TAM($html)
{
	global $NAME_TAM;

	if(!preg_match('/<strong id="eTicketName"[^>]*>(?P<name>[^<]+)<\/strong>/', $html, $match))
		return '';

	$name = explode('&nbsp;', trim($match['name']));
	return strtoupper($name[2] . '/' . $name[1] . ' ' . $NAME_TAM[$name[0]]);
}
?>
