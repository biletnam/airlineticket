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

# Manipulate input ticket data

################################################################################


# Return all tickets queued for checking as an associative array
# Currently reads JSON files, later it could be from database or RPC call
function list_tickets()
{
	$adata = array();
	$filenames = glob(dirname(__FILE__) . "/data/*.json");
	foreach($filenames as $filename)
		if ($data = @json_decode(@file_get_contents($filename), TRUE))
			if (isset($data['companhia']) && isset($data['ticket']))
				$adata["${data['companhia']}.${data['ticket']}"] = $data;
	return $adata;
}


# Remove a ticket from the checking queue
# Currently deletes its JSON file from the data directory
function remove_ticket($key)
{
	return unlink(dirname(__FILE__) . "/data/${key}.json");
}
?>
