<?php
/**
 * ORM of the table protocol
 */
class protocol extends ObjetBDD
{

    /**
     *
     * @param PDO $bdd
     * @param array $param
     */
    function __construct($bdd, $param = array())
    {
        $this->table = "protocol";
        $this->colonnes = array(
            "protocol_id" => array(
                "type" => 1,
                "key" => 1,
                "requis" => 1,
                "defaultValue" => 0
            ),
            "protocol_name" => array(
                "type" => 0,
                "requis" => 1
            ),
            "protocol_url" => array("type" => 0),
            "protocol_description" => array("type" => 0),
            "measure_default" => array('type' => 0, "requis" => 1),
            "measure_default_only" => array("type" => 1, "requis" => 1),
            "analysis_template_id" => array("type" => 1)
        );
        parent::__construct($bdd, $param);
    }

    /**
     * Return the list of the protocols
     *
     * @return array
     */
    function getListProtocol()
    {
        $sql = "select protocol_id, protocol_name, protocol_url, protocol_description,
        measure_default, measure_default_only, 
        analysis_tempate_id, analysis_template_name
        from protocol
        order by protocol_name";
        return $this->getListeParam($sql);
    }

    /**
     * Get the detail of a protocol with attached analysis_template
     *
     * @param int $protocol_id
     * @return array
     */
    function getDetail($protocol_id)
    {
        $sql = "select protocol_id, protocol_name, protocol_url, protocol_description
                , measure_default, measure_default_only
                ,analysis_template_id, analysis_template_name, analysis_template_value
                from protocol
                left outer join analysis_template using (analysis_template_id)
                where protocol_id = :protocol_id";
        return $this->lireParamAsPrepared($sql, array("protocol_id" => $protocol_id));
    }
}