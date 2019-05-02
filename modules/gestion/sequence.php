<?php
require_once 'modules/classes/sequence.class.php';
$dataClass = new Sequence($bdd, $ObjetBDDParam);
$keyName = "sequence_id";
$id = $_SESSION["ti_sequence"]->getValue($_REQUEST[$keyName]);
$campaign_id = $_SESSION["ti_campaign"]->getValue($_REQUEST["campaign_id"]);
$operation_id = $_SESSION["ti_operation"]->getValue($_REQUEST["operation_id"]);
if (isset($_REQUEST["activeTab"])) {
    $activeTab = $_REQUEST["activeTab"];
}

switch ($t_module["param"]) {

    case "display":
        $data = $_SESSION["ti_sequence"]->translateRow($dataClass->getDetail($id));
        $vue->set($_SESSION["ti_campaign"]->translateRow($data), "data");
        $vue->set("gestion/sequenceDisplay.tpl", "corps");
        /**
         * related lists
         */
        require_once 'modules/classes/sequence_gear.class.php';
        $sg = new SequenceGear($bdd, $ObjetBDDParam);
        $vue->set(
            $_SESSION["ti_sequenceGear"]->translateList(
                $_SESSION["ti_sequence"]->translateList(
                    $sg->getListFromSequence($id)
                )
                ),
                "gears"
        );
        /**
         * select the good tab for display
         */
        if (isset($activeTab)) {
            $vue->set($activeTab, "activeTab");
        }
        break;

    case "change":
        /*
         * open the form to modify the record
         * If is a new record, generate a new record with default value :
         * $_REQUEST["idParent"] contains the identifiant of the parent record
         */
        $data = dataRead($dataClass, $id, "gestion/sequenceChange.tpl", $operation_id);
        if ($data["sequence_id"] == 0) {
            /**
             * New sequence
             */
            $data["sequence_number"] = $dataClass->getLastSequenceNumber($operation_id);
        }
        $data["campaign_id"] = $campaign_id;
        $data = $_SESSION["ti_campaign"]->translateRow($data);
        $data = $_SESSION["ti_operation"]->translateRow($data);
        $vue->set($_SESSION["ti_sequence"]->translateRow($data), "data");
        /**
         * Preparation of the parameters tables
         */
        $params = array("water_regime", "fishing_strategy", "scale", "taxa_template", "protocol");
        foreach ($params as $tablename) {
            setParamToVue($vue, $tablename);
        }
        break;
    case "write":
        /*
         * write record in database
         */
        $data = $_SESSION["ti_campaign"]->translateFromRow($_REQUEST);
        $data = $_SESSION["ti_operation"]->translateFromRow($data);
        $data["sequence_id"] = $id;
        $id = dataWrite($dataClass, $data);
        if ($id > 0) {
            $_REQUEST[$keyName] = $_SESSION["ti_sequence"]->setValue($id);
        }
        $activeTab = "tab-sequence";
        break;
    case "delete":
        /*
         * delete record
         */

        dataDelete($dataClass, $id);
        $activeTab = "tab-sequence";
        break;
}
