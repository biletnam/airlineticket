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


$old      = 0;  # Old ticket, departure date in the past
$match    = 0;  # Ticket found and its data matches
$mismatch = 0;  # Ticket found but its data does not match
$notfound = 0;  # Ticket was not found
$error    = 0;  # Error trying to access ticket data

foreach (list_tickets() as $key => $data) {

	# Ignore old tickets
	if(strtotime($data['saida']) < time()) {
		$old++;
		continue;
	}

	echo("\n${key}\n");
	print_r($data);

	$match++;
}

printf("
OK       : %3d
Old      : %3d
Mismatch : %3d
Error    : %3d
NotFound : %3d
Total    : %3d
",
$match,  $old,  $mismatch,  $error,  $notfound,
$match + $old + $mismatch + $error + $notfound
);
?>
