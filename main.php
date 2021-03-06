#!/usr/bin/php
<?php
################################################################################
#
# AirlineTicket - Check ticket info on airline websites
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

# Main script

################################################################################

include "data.php";
include "scrapper.php";


$old       = 0;  # Old ticket, departure date in the past
$match     = 0;  # Ticket found and all of its data matches
$fees      = 0;  # Ticket found and all but 'taxas' field matches
$mismatch  = 0;  # Ticket found but its data does not match
$notfound  = 0;  # Ticket was not found
$cancelled = 0;  # Ticket was cancelled
$error     = 0;  # Error trying to access ticket data


setlocale(LC_ALL, "en_US.utf8");
date_default_timezone_set('UTC');


foreach (list_tickets() as $key => $data) {

#	if($data['companhia'] != 'TAM')
#		continue;

	# Ignore old tickets
	if(strtotime($data['saida']) < time()) {
		$old++;
		continue;
	}

	echo("Ticket ${key}: ");

	$web = scrape_ticket($data['companhia'], $data['ticket'], $data['passageiro']);
	if(is_array($web) && isset($web['error'])) {
		echo($web['error'] . "\t" . $data['passageiro'] . "\n");
		switch($web['error']) {
			case 'cancelled': $cancelled++; break;
			case 'not found': $notfound++ ; break;
			default:          $error++;
		}
		continue;
	}

	if(($fields = check_fields($data, $web))) {
		echo("data mismatch: " . implode($fields, ', ') . "\n");
		$onlyfees = true;
		foreach($fields as $field)
			if($field != 'taxas') {
				$onlyfees = false;
				break;
			}
		if($onlyfees)
			$fees++;
		else {
			print_r($data);
			print_r($web);
			echo("\n");
			$mismatch++;
		}
		continue;
	}

	echo("OK!\n");
	$match++;
}

printf("
OK       : %3d
Old      : %3d
Fees     : %3d
Mismatch : %3d
Error    : %3d
NotFound : %3d
Cancelled: %3d
Total    : %3d
",
$match,  $old,  $fees,  $mismatch,  $error,  $notfound,  $cancelled,
$match + $old + $fees + $mismatch + $error + $notfound + $cancelled
);
?>
