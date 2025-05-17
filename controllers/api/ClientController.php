<?php
class ClientController {
    private $client;

    public function __construct($client)
    {
        $this->client = new Client($client);
    }

    public function HandleRequestProfil($telephone) {
        $data = $this->client->getUser($telephone);
        if (!empty($data)) {
            echo json_encode($data);
        } else {
            http_response_code(404);
            echo json_encode([
                'error' => 'Client inexistant'
            ]);
        }
        
    }
    public function HandleRequestOperation($telephone) {
        $data = $this->client->getOperations($telephone);
        if (!empty($data)) {
            echo json_encode($data);
        } else {
            http_response_code(404);
            echo json_encode([
                'error' => 'Client inexistant'
            ]);
        }
        
    }

    public function HandleRequestCreate() {
        $reponse = $this->client->createUser();        
        echo json_encode($reponse);        
    }
}