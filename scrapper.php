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

# Scrapper manager

################################################################################

include "scrappers/azul.php";
include "scrappers/tam.php";


function scrape_ticket($airline, $pnr, $name)
{
	switch($airline) {
		case "AZUL"   : return scrape_AZUL   ($pnr, $name);
		case "TAM"    : return scrape_TAM    ($pnr, $name);
#		case "GOL"    : return scrape_GOL    ($pnr, $name);
#		case "AVIANCA": return scrape_AVIANCA($pnr, $name);
#		case "TAP"    : return scrape_TAP    ($pnr, $name);
		default:
			return array('error' => "No scrapper for airline ${airline}!");
	}
}
?>
