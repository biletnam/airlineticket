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


# Check each field for data mismatch, recursively
function check_fields($a1, $a2) {
	$d = array();
	foreach($a1 as $k => $v1) {
		if(!isset($a2[$k])) {
			$d[] = $k;
			continue;
		}
		$v2 = $a2[$k];
		if(is_array($v1)) {
			if (is_array($v2))
				foreach(check_fields($v1, $v2) as $s)
					$d[] = "$k/$s";
			else
				$d[] = $k;
			continue;
		}
		if($v1 != $v2)
			$d[] = $k;
	}
	return $d;
}
?>
