<?php
require_once 'modules/classes/analysis.class.php';
$dataClass = new Analysis($bdd, $ObjetBDDParam);
$keyName = "analysis_id";
$id = $_SESSION["ti_analysis"]->getValue($_REQUEST[$keyName]);
if (strlen($id) == 0) {
    $id = 0;
}
$sequence_id = $_SESSION["ti_sequence"]->getValue($_REQUEST["sequence_id"]);

switch ($t_module["param"]) {
    case "change":
        /*
     * open the form to modify the record
     * If is a new record, generate a new record with default value :
     * $_REQUEST["idParent"] contains the identifiant of the parent record 
     */
        $data = dataRead($dataClass, $id, "gestion/analysisChange.tpl", $sequence_id);
        require_once "modules/classes/sequence.class.php";
        $sequence = new Sequence($bdd, $ObjetBDDParam);
        $dataSequence = $_SESSION["ti_operation"]->translateRow(
            $_SESSION["ti_campaign"]->translateRow(
                $_SESSION["ti_sequence"]->translateRow(
                    $sequence->getDetail($sequence_id)
                )
            )
        );
        $vue->set(
            $dataSequence,
            "sequence"
        );
        if ($data["analysis_id"] == 0) {
            /**
             * Create a new record
             */
            $data["analysis_date"] = $dataSequence["date_start"];
            $data["sequence_id"] = $sequence_id;
        }
        $vue->set(
            $_SESSION["ti_sequence"]->translateRow(
                $_SESSION["ti_analysis"]->translateRow(
                    $data
                )
            ),
            "data"
        );
        break;
    case "write":
        /*
     * write record in database
     */
        $id = dataWrite($dataClass, $_REQUEST);
        if ($id > 0) {
            $_REQUEST[$keyName] = $id;
        }
        break;
    case "delete":
        /*
     * delete record
     */
        dataDelete($dataClass, $id);
        break;
}
