<?php
namespace App\Service;

use App\Models\Portal;
use App\Models\PortalXML;
use App\Models\PortalConditions;
use Exception;

class XmlService {

    public function createXML() {
        foreach($this->getPortals() as $portal) {
            $xmlPortal = $this->getXmlBasedInPortalId($portal->id);
            $portalConditions = $this->getPortalConditions($portal->id);
            return $this->makeContentWithConditions($portal, $xmlPortal, $portalConditions);
        }
    }

    private function getPortals(): array {
        return Portal::find();
    }

    private function getXmlBasedInPortalId(int $id) {
        return PortalXML::findById($id);
    }

    private function getPortalConditions(int $id) {
        return PortalConditions::find($id);
    }

    private function makeContentWithConditions($portal, $xmlPortal, $portalConditions) {
        //aqui voce geraria os dados de cada imovel para ser usados na condição
        $data = $this->makeData();
        $contentValuesMap = $this->getListValues($xmlPortal);

        $defaultContent = $xmlPortal->content;

        foreach($contentValuesMap as $contentMap) {
            $conditions = array_filter($portalConditions, function($portalCondition) use ($contentMap) {
                return $portalCondition->field === $contentMap;
            });

            if (empty($conditions)) continue;

            $passAllConditions = false;
            foreach($conditions as $condition) {
                $valueToCompare = $data[$condition->value_to_compare];
                $valueCompared = $condition->value_compared;
                
                switch ($condition->modifier) {
                    case 'IGUAL': $passAllConditions = $valueToCompare == $valueCompared;
                        break;
                    case 'MAIOR': $passAllConditions = $valueToCompare > $valueCompared;
                        break;
                    default: throw new Exception('Algo De Errado Rolou');
                }
            }

            if(!$passAllConditions) continue;

            $pattern = "/#\[(".$condition->field."\|40)\]#/";
            $value = $data[$condition->value];
            if (!empty($condition->funcion_auxiliar)) {

                $call = $condition->funcion_auxiliar;
                $value = $this->$call($value);
            }
            $xmlPortal->content = preg_replace($pattern,$value,$xmlPortal->content);

            header('Content-Type: application/xml');
            
            return $xmlPortal->header.$xmlPortal->content.$xmlPortal->footer;
        }
    }

    private function concatValue($value) {
        return $value.'u';
    }

    private function makeData(): array {
        return array(
            'codigo' => 99901,
            'IMOVEL_TIPO' => 'C',
            'tipo_imovel_texto' => 'Casa',
            'IMOVEL_DORMITORIOS' => 11,
            'IMOVEL_VAGAS' => 2
        );
    }

    private function getListValues($xmlPortal): array {
        $matches = [];
        preg_match_all('#\[(.*?)\]#',$xmlPortal->content, $matches, PREG_SET_ORDER);

        return array_map(function($matche) {
            return explode('|', $matche[1])[0];
        }, $matches);
    }
}