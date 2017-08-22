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
		case 'Booking Invalid':  # Not found
		case 'noJourneysError':  # Cancelled
		default:
			return $json;
	}

	return $data;
}
?>
