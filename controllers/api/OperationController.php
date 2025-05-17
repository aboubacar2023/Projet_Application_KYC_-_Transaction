<?php
class OperationController {
    private $operation;

    public function __construct($operation)
    {
        $this->operation = new Operation($operation);
    }

    public function HandleRequestDepot() {
        $reponse = $this->operation->depot();
        echo json_encode($reponse);
        
    }
    public function HandleRequestRetrait() {
        $reponse = $this->operation->retrait();
        echo json_encode($reponse);
        
    }
    public function HandleRequestTransfert() {
        $reponse = $this->operation->transfert();
        echo json_encode($reponse);
        
    }
}