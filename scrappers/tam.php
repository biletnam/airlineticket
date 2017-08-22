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
	$data = array();
	return $data;
}


function extract_TAM($data, $json)
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

	$data = array();
	return $data;
}
?>
