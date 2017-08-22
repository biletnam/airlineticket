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

# Scrapper for AZUL

################################################################################


function scrape_AZUL($pnr, $name)
{
	$url = 'https://viajemais.voeazul.com.br/RetrieveBookingAjax.aspx';
	$data = array();

	# Get first passenger's "last name", ie, all but the first name
	$name = explode(', ', $name);
	$name = explode(' ', $name[0]);
	$name = implode(' ', array_slice($name, 1));
	$name = urlencode($name);

	$url .= "?culture=pt-br&_authkey_=&pnr=${pnr}&lastName=${name}";
	$json = json_decode(file_get_contents($url), TRUE);

	switch($json['Message']) {
		case 'sucesso': return extract_AZUL($data, $json['ReturnObject']);
		case 'Booking Invalid':  # Not found
		case 'noJourneysError':  # Cancelled
		default:
			return $json;
	}

	return $data;
}


function extract_AZUL($data, $json)
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

	$idaevolta = (count($json['ItineraryJourneyList']) > 1);

	$ida   =              $json['ItineraryJourneyList'][0];
	$volta = $idaevolta ? $json['ItineraryJourneyList'][1] : false;

	$idasaida   = $ida['SegmentList'][0];
	$idachegada = $ida['SegmentList'][count($ida['SegmentList'])-1];

	$data['companhia']  = 'AZUL';
	$data['ticket']     = $json['RecordLocator'];

	$data['origem']     = $ida['Departure'];  #  Rio de Janeiro - Santos Dumont (SDU)
	$data['destino']    = $ida['Arrival'];    #  Belo Horizonte - Confins (CNF)

	# Dates and likely other fields are formatted using locale $json['CultureCode'] == 'pt-BR'
	$data['saida']      = date('Y-m-d H:i', strtotime($idasaida['DepartureDate']));  # 19/08/2017
	$data['saida']     .= ' ' . $idasaida['DepartureTime'];  # 10:15

	$data['idaevolta']  = $idaevolta;

	$data['milhas']     = $json['TotalPoints'];
	$data['taxas']      = $json['TotalMoney'];
	$data['moeda']      = $json['CurrencyCode'];

	$data['voo']        = $idasaida['FlightNumber'];  # 2590

	$apass = array();
	foreach($json['ItineraryPassengerList'] as $pass) {
		$apass[] =
			name_AZUL($pass['FirstName']) . ' ' .
			($pass['MiddleName'] ? name_AZUL($pass['MiddleName']) . ' ' : '') .
			name_AZUL($pass['LastName']);
	}
	$data['passageiro'] = implode(', ', $apass);

	# Other fields of interest
	$data[''] = $json['ItineraryJourneyList'][0]['DepartureIATA'];  # SDU
	$data[''] = $json['ItineraryJourneyList'][0]['SegmentList'][0]['CarrierCode'];  # AD

	return $data;
}


function name_AZUL($name)
{
	return ucfirst(strtolower($name));
}
?>
