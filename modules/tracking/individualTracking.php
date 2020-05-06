<?php
include_once 'modules/classes/tracking/individual_tracking.class.php';
$dataClass = new IndividualTracking($bdd, $ObjetBDDParam);
$keyName = "individual_id";
$id = $_REQUEST[$keyName];
require_once 'modules/classes/individual.class.php';
$individual = new Individual($bdd, $ObjetBDDParam);

switch ($t_module["param"]) {
  case "list":
    include_once 'modules/classes/project.class.php';
    $project = new Project($bdd, $ObjetBDDParam);
    isset($_COOKIE["projectId"]) ? $project_id = $_COOKIE["projectId"] : $project_id = 0;
    isset($_COOKIE["projectActive"]) ? $is_active = $_COOKIE["projectActive"] : $is_active = 1;
    $vue->set($projects = $project->getProjectsActive($is_active, $_SESSION["projects"]), "projects");
    $vue->set($is_active, "is_active");
    if ($project_id > 0 && !verifyProject($project_id)) {
      $project_id = $projects[0]["project_id"];
    }
    if (!$project_id > 0) {
      $project_id = $projects[0]["project_id"];
    }
    $vue->set($dataClass->getListFromProject($project_id), "individuals");
    $vue->set("tracking/individualTrackingList.tpl", "corps");
    $vue->set($project_id, "project_id");
    if ($_REQUEST["individual_id"] > 0) {
      $dindividual = $dataClass->lire($_REQUEST["individual_id"]);
      if ($dindividual["project_id"] == $project_id) {
        $vue->set($dataClass->getListDetection($_REQUEST["individual_id"], 'YYYY-MM-DD HH24:MI:SS.MS', "detection_date asc"), "detections");
        $vue->set($_REQUEST["individual_id"], "selectedIndividual");
        setParamMap($vue, false);
      }
    }
    break;
  case "change":
    if ($_REQUEST["individual_id"] == 0) {
      /**
       * Set the project from the cookie
       */
      $_REQUEST["project_id"] = $_COOKIE["projectId"];
    }
    if (verifyProject($_REQUEST["project_id"])) {
      $data = dataRead($individual, $id, "tracking/individualTrackingChange.tpl", $_REQUEST["project_id"]);
      require_once 'modules/classes/sexe.class.php';
      $sexe = new Sexe($bdd, $ObjetBDDParam);
      $vue->set($sexe->getListe(1), "sexes");
      require_once 'modules/classes/pathology.class.php';
      $pathology = new Pathology($bdd, $ObjetBDDParam);
      $vue->set($pathology->getListe(3), "pathologys");
      include_once 'modules/classes/tracking/transmitter_type.class.php';
      $tt = new TransmitterType($bdd, $ObjetBDDParam);
      $vue->set($tt->getListe("transmitter_type_name"), "transmitters");
      /**
       * Set the project
       */
      include_once 'modules/classes/project.class.php';
      $project = new Project($bdd, $ObjetBDDParam);
      $vue->set($project->getDetail($_REQUEST["project_id"]), "project");
    } else {
      $module_coderetour = -1;
      $message->set(_("Le projet indiqué ne fait pas partie des projets qui vous sont autorisés"), true);
    }
    break;
  case "write":
    if (verifyProject($_REQUEST["project_id"])) {
      try {
        $id == 0 ? $isNew = true : $isNew = false;
        $bdd->beginTransaction();
        $id = dataWrite($individual, $_REQUEST, true);
        if ($id > 0) {
          if ($isNew) {
            $t_module["retourok"] = "individualTrackingChange";
            $_REQUEST[$keyName] = 0;
          } else {
            $_REQUEST[$keyName] = $id;
          }
        }
        $module_coderetour = 1;
        $bdd->commit();
      } catch (Exception $e) {
        $bdd->rollback();
        $message->set(_("Problème rencontré lors de l'enregistrement du poisson"), true);
        $message->setSyslog($e->getMessage());
        $module_coderetour = -1;
      }
    } else {
      $module_coderetour = -1;
      $message->set(_("Le projet indiqué ne fait pas partie des projets qui vous sont autorisés"), true);
    }
    break;
  case "delete":
    if (verifyProject($_REQUEST["project_id"])) {
      try {
        $bdd->beginTransaction();
        dataDelete($individual, $id, true);
        $bdd->commit();
        $module_coderetour = 1;
      } catch (Exception $e) {
        $bdd->rollback();
        $message->set(_("Problème rencontré lors de la suppression du poisson"), true);
        $message->setSyslog($e->getMessage());
        $module_coderetour = -1;
      }
    } else {
      $module_coderetour = -1;
      $message->set(_("Le projet indiqué ne fait pas partie des projets qui vous sont autorisés"), true);
    }
    break;
  case "export":
    /**
     * Export the list of detections for all selected fish
     */
    try {
      if (verifyProject($_POST["project_id"])) {
        if (is_array($_POST["uids"])) {
          $uid = $_POST["uids"];
        } else {
          $uid = array($_POST["uids"]);
        }
        if ($uid[0] > 0) {
          $vue->set($dataClass->getListDetection($uid));
        } else {
          throw new IndividualTrackingException(_("Aucun poisson n'a été sélectionné"));
        }
      } else {
        throw new IndividualTrackingException(_("Le projet indiqué ne fait pas partie des projets qui vous sont autorisés"));
      }
    } catch (IndividualTrackingException $ite) {
      $message->set($ite->getMessage(), true);
      $module_coderetour = -1;
      unset($vue);
    }
    break;
}
