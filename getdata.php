<?php
require_once 'vendor/autoload.php';

$which = strtoupper($_GET['type']);
$vatsim = new \Vatsimphp\VatsimData();

if ($vatsim->loadData()) {

    switch ($which) {
        case 'ALL':
            $result = $vatsim->getClients()->toArray();
            break;
        case 'ATC':
            $result = $vatsim->getControllers()->toArray();
            break;
        case 'PILOT':
            $result = $vatsim->getPilots()->toArray();
            break;
        case 'INFO':
            $result = $vatsim->getGeneralInfo()->toArray();
            break;
        case 'SERVERS':
            $result = $vatsim->getServers()->toArray();
            break;
        case 'CALLSIGN':
            $callsign = strtoupper($_GET['q']);
            $result = $vatsim->searchCallsign($callsign)->toArray();
            break;
        case 'CID':
            $cid = strtoupper($_GET['q']);
            $result = $vatsim->searchVatsimId($cid)->toArray();
            break;
        case 'METAR':
            $metar = strtoupper($_GET['q']);
            $result = $vatsim->getMetar($metar);
            break;
    }

    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    echo json_encode($result);
} else {
    echo json_encode('666');
}
